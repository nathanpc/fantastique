<?php

namespace Fantastique;

use Fantastique\Exceptions\Exception;
use Fantastique\Exceptions\PathException;

/**
 * Helper class to aid in building the website generator.
 */
class Builder {
	public readonly string $base_path;
	public readonly string $output_path;

	/**
	 * Constructs a new website builder helper object.
	 *
	 * @param string $base_path   Base path of the website source files.
	 * @param string $output_path Root folder to store the static website files.
	 */
	public function __construct(string $base_path, string $output_path) {
		$this->base_path = $base_path;
		$this->output_path = $output_path;
	}

	/**
	 * Duplicates a folder with static assets to serve as the basis for the
	 * build directory.
	 *
	 * @param string $static_folder Static folder to duplicate as the basis of
	 *                              the build directory.
	 *
	 * @throws PathException if a file operation fails.
	 */
	public function copy_static(string $static_folder): void {
		echo "Copying static files from $static_folder\n";
		$this->copy_folder(realpath($static_folder), $this->output_path);
	}

	/**
	 * Renders the contents of an entire folder.
	 *
	 * @param string $folder    Folder to be rendered out.
	 * @param bool   $recursive Should we also render the contents of its
	 *                          sub-folders?
	 * @param ?array $exclude   List of file or folder names to not render.
	 *
	 * @throws Exception if an error occurs while rendering a page.
	 */
	public function render_folder(string $folder, bool $recursive = true,
	                              array $exclude = null,
	                              array $context = null): void {
		echo "Entering $folder\n";

		// Open the directory and go through its contents.
		$dh = opendir($folder);
		while (($fname = readdir($dh)) !== false) {
			// Ignore current and parent directory.
			if (($fname === '.') || ($fname === '..'))
				continue;

			// Ignore the ignored.
			if (!is_null($exclude)) {
				foreach ($exclude as $fnexclude) {
					if ($fname === $fnexclude)
						goto skip;
				}
			}

			// Recurse over directories.
			$fpath = "$folder/$fname";
			if (is_dir($fpath)) {
				if ($recursive)
					$this->render_folder($fpath, true, $exclude, $context);
				continue;
			}

			// Render the page.
			$this->render_page($fpath, $context);
			skip:
		}

		// Close the directory handle.
		closedir($dh);
	}

	/**
	 * Creates a new page object from a source file path.
	 *
	 * @param string $source Path to the page's source file.
	 *
	 * @return Page Generated page object.
	 *
	 * @throws Exception if an error occurs while generating the page.
	 */
	public function make_page(string $source): Page {
		return new Page($this->base_path, $source);
	}

	/**
	 * Creates a new page object from a source file path and renders it.
	 *
	 * @param string $source  Path to the page's source file.
	 * @param ?array $context Extra context to the page to be rendered.
	 *
	 * @return Page Rendered page object.
	 *
	 * @throws Exception if an error occurs while rendering the page.
	 */
	public function render_page(string $source, array $context = null): Page {
		return $this->make_page($source)->render($this->output_path, $context);
	}

	/**
	 * Copies the contents of an entire folder to another location recursively.
	 *
	 * @param string $source Source folder to be copied over.
	 * @param string $dest   Destination folder.
	 *
	 * @throws PathException if a file operation fails.
	 */
	protected function copy_folder(string $source, string $dest): void {
		// Ensure we have a destination folder.
		if (!is_dir($dest)) {
			if (!mkdir($dest, 0755, true)) {
				throw new PathException("Failed to create directory ($dest) " .
					'for copying');
			}
		}

		// Open the directory and go through its contents.
		$dh = opendir($source);
		while (($fname = readdir($dh)) !== false) {
			// Ignore current and parent directory.
			if (($fname === '.') || ($fname === '..'))
				continue;

			// Make source and destination paths.
			$src_path = "$source/$fname";
			$dest_path = "$dest/$fname";

			// Copy file or recurse over directories.
			if (is_dir($src_path)) {
				echo "Copying folder $src_path to $dest_path\n";
				$this->copy_folder($src_path, $dest_path);
			} else {
				echo "Copying static file from $src_path to $dest_path\n";
				if (!copy($src_path, $dest_path)) {
					throw new PathException('Failed to copy file from ' .
						"$src_path to $dest_path");
				}
			}
		}

		// Close the directory handle.
		closedir($dh);
	}
}
