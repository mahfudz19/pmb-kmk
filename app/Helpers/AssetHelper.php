<?php

if (!function_exists('asset')) {
  /**
   * Get the path to a versioned asset file.
   *
   * @param string $path
   * @return string
   */
  function asset($path)
  {
    static $manifest = null;

    if ($manifest === null) {
      $manifestPath = __DIR__ . '/../../public/build/manifest.json';
      if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
      } else {
        $manifest = [];
      }
    }

    $path = ltrim($path, '/');
    $altPath = str_replace('/', '\\', $path);
    $altPath2 = str_replace('\\', '/', $path);

    if (isset($manifest[$path])) {
      return getBaseUrl('build/' . $manifest[$path]);
    }
    if (isset($manifest[$altPath])) {
      return getBaseUrl('build/' . $manifest[$altPath]);
    }
    if (isset($manifest[$altPath2])) {
      return getBaseUrl('build/' . $manifest[$altPath2]);
    }

    $filePath = __DIR__ . '/../../public/build/' . $path;
    if (!file_exists($filePath)) {
      $filePath = __DIR__ . '/../../public/build/' . $altPath;
    }
    if (!file_exists($filePath)) {
      $filePath = __DIR__ . '/../../public/build/' . $altPath2;
    }
    $version = file_exists($filePath) ? filemtime($filePath) : time();
    return getBaseUrl('build/' . str_replace('\\', '/', $path)) . '?v=' . $version;
  }
}
