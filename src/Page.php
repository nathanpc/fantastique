<?php

namespace Fantastique;

use Fantastique\Exceptions\PathException;
use Fantastique\Exceptions\RenderException;

/**
 * The main abstraction of a static page to be generated.
 */
class Page {
	public ?string $title;
	public ?string $description;
	public string $path;
	public string $filename;
	public string $source;
	public ?array $context;

	/**
	 * Sets up a page for static generation using a source file.
	 *
	 * @param string $base_path Base path for the static structure.
	 * @param string $source    Path to source file for this page.
	 *
	 * @throws PathException if the base path and the file path are unrelated.
	 */
	public function __construct(string $base_path, string $source) {
		$this->source = realpath($source);
		$this->title = null;
		$this->description = null;
		$this->filename = 'index.html';
		$this->context = null;

		// Build up the path.
		$path_parts = pathinfo($this->relpath($base_path, $source));
		if ($path_parts['dirname'] === '/')
			$path_parts['dirname'] = '';
		$this->path = "{$path_parts['dirname']}/{$path_parts['filename']}";
	}

	/**
	 * Fills up properties from the side of the page's source.
	 *
	 * @param array $props Properties of the object to be assigned values.
	 */
	public function fill(array $props): void {
		foreach ($props as $prop => $value) {
			$this->$prop = $value;
		}
	}

	/**
	 * Renders the file to its final destination.
	 *
	 * @param string $output_root Path of the static website output folder.
	 * @param ?array $context     Extra context to the page to be rendered.
	 *
	 * @return Page Ourselves for composability reasons.
	 *
	 * @throws PathException if we cannot make the directory to output to.
	 * @throws RenderException if something goes wrong with rendering.
	 */
	public function render(string $output_root, array $context = null): Page {
		$content = null;
		echo "Rendering {$this->source} to $output_root{$this->path}/" .
			"{$this->filename}\n";

		// Ensure we have some context.
		$this->context = is_null($this->context) ? $context :
			array_merge($this->context, $context);

		try {
			// Render the template out to a string.
			ob_start();
			include($this->source);
			$content = ob_get_contents();
			ob_end_clean();
		} catch (\Exception $e) {
			throw new RenderException('Rendering failed due to an exception: ' .
				$e, $this, $e);
		}

		// Ensure we have the directory to output to.
		$outdir = "$output_root{$this->path}";
		if (!is_dir($outdir)) {
			if (!mkdir($outdir, 0755, true)) {
				throw new PathException('Failed to create directory ' .
					"($outdir) to render to");
			}
		}

		// Write the rendered contents to a file.
		$outfile = "$outdir/{$this->filename}";
		if (file_put_contents($outfile, $content) === false) {
			throw new RenderException('Failed to write the rendered contents ' .
				"to the file ($outfile)", $this);
		}

		return $this;
	}

	/**
	 * Builds a relative path based on a base path and a file path.
	 *
	 * @param string $base_path Relative path base/root.
	 * @param string $file_path Path to a file to be relativized.
	 *
	 * @return string Path to the file relative to the base path.
	 *
	 * @throws PathException if the base path and the file path are unrelated.
	 */
	protected function relpath(string $base_path, string $file_path): string {
		$base_path = realpath($base_path);
		$file_path = realpath($file_path);

		// Check if the base path is actually related to the file path.
		if (!str_starts_with($file_path, $base_path))
			throw new PathException("Base path and file path are unrelated");

		// Get the relative path.
		$relpath = substr($file_path, strlen($base_path));

		// Handle the special case of the index.
		if (basename($file_path) === 'index.php')
			return dirname($relpath);

		return $relpath;
	}
}
