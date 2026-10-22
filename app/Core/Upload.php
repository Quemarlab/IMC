<?php
declare(strict_types=1);

namespace YM\Core;

/**
 * Hardened image uploads: size cap, real MIME sniffing (finfo + getimagesize),
 * random server-side filenames, and — when GD is available — a re-encode that
 * downsizes and strips metadata/embedded payloads.
 */
final class Upload
{
    public const MAX_BYTES = 8 * 1024 * 1024;
    private const TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const MAX_EDGE = 1920;

    public static function dir(string $folder): string
    {
        return YM_ROOT . '/ControlPanel/upload/' . $folder;
    }

    /**
     * @param array $file One entry of $_FILES
     * @return string|null Stored filename, or null when no file was submitted
     * @throws \RuntimeException with a user-presentable message
     */
    public static function image(array $file, string $folder): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('The upload failed (error ' . (int) $file['error'] . '). Try a smaller file.');
        }
        if ($file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('Images must be 8 MB or smaller.');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Invalid upload.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::TYPES[$mime]) || @getimagesize($file['tmp_name']) === false) {
            throw new \RuntimeException('Only JPG, PNG or WebP images are allowed.');
        }

        $dir = self::dir($folder);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('Upload folder is not writable.');
        }

        $name = bin2hex(random_bytes(12)) . '.' . self::TYPES[$mime];
        $target = $dir . '/' . $name;

        if (!self::reencode($file['tmp_name'], $target, $mime) && !move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Could not save the uploaded file.');
        }
        return $name;
    }

    private static function reencode(string $src, string $dest, string $mime): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }
        $img = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($src),
            'image/png' => @imagecreatefrompng($src),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($src) : false,
            default => false,
        };
        if (!$img) {
            return false;
        }
        [$w, $h] = [imagesx($img), imagesy($img)];
        if (max($w, $h) > self::MAX_EDGE) {
            $scale = self::MAX_EDGE / max($w, $h);
            $resized = imagescale($img, (int) round($w * $scale), (int) round($h * $scale));
            if ($resized) {
                $img = $resized;
            }
        }
        if ($mime === 'image/png') {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }
        return match ($mime) {
            'image/jpeg' => imagejpeg($img, $dest, 85),
            'image/png' => imagepng($img, $dest, 7),
            'image/webp' => imagewebp($img, $dest, 85),
        };
    }

    /** Deletes a stored file; the name is reduced to a basename to block traversal. */
    public static function delete(string $folder, ?string $file): void
    {
        if (!$file) {
            return;
        }
        $path = self::dir($folder) . '/' . basename($file);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
