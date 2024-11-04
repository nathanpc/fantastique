<?php

namespace Fantastique;

use Fantastique\Exceptions\Exception;

/**
 * Helper class to aid in building the website generator.
 */
class Builder {
	private string $base_path;
	private string $output_path;

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
	 * Renders the contents of an entire folder.
	 *
	 * @param string $folder Folder to be rendered out.
	 * @param bool $recursive Should we also render the contents of its
	 *                              sub-folders?
	 * @param array|null $exclude List of file or folder names to not render.
	 *
	 * @throws Exception if an error occurs while rendering a page.
	 */
	public function render_folder(string $folder, bool $recursive = true,
	                              ?array $exclude = null): void {
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
					$this->render_folder($fpath, true, $exclude);
				continue;
			}

			// Render the page.
			$this->render_page($fpath);
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
	 * @throws Exceptions\Exception if an error occurs while generating the page.
	 */
	public function make_page(string $source): Page {
		return new Page($this->base_path, $source);
	}

	/**
	 * Creates a new page object from a source file path and renders it.
	 *
	 * @param string $source Path to the page's source file.
	 *
	 * @return Page Rendered page object.
	 *
	 * @throws Exceptions\Exception if an error occurs while rendering the page.
	 */
	public function render_page(string $source): Page {
		return $this->make_page($source)->render($this->output_path);
	}
}
