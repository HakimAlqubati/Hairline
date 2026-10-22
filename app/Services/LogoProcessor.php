<?php

declare(strict_types=1);

namespace App\Services;

class LogoProcessor
{
    public static function ensureLogosExist(): void
    {
        $darkLogoPath = public_path('images/logo-dark.png');
        $lightLogoPath = public_path('images/logo.png');
        $backupPath = public_path('images/logo_original_backup.jpg');

        // Only process if dark logo doesn't exist yet
        if (file_exists($darkLogoPath) && file_exists($lightLogoPath) && file_exists($backupPath)) {
            return;
        }

        if (! file_exists($lightLogoPath) && ! file_exists($backupPath)) {
            return;
        }

        // Keep backup of original before any modification
        if (! file_exists($backupPath) && file_exists($lightLogoPath)) {
            copy($lightLogoPath, $backupPath);
        }

        $sourceFile = file_exists($backupPath) ? $backupPath : $lightLogoPath;
        $rawData = file_get_contents($sourceFile);
        if (! $rawData) {
            return;
        }

        $src = @imagecreatefromstring($rawData);
        if (! $src) {
            return;
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // Detect stroke gold color
        $minBrightness = 999;
        $goldR = 186;
        $goldG = 149;
        $goldB = 92;

        for ($y = 0; $y < $height; $y += 2) {
            for ($x = 0; $x < $width; $x += 2) {
                $rgb = imagecolorat($src, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $brightness = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                if ($brightness < $minBrightness && $r > 100 && $g > 70 && $b > 30) {
                    $minBrightness = $brightness;
                    $goldR = $r;
                    $goldG = $g;
                    $goldB = $b;
                }
            }
        }

        // Bounding box detection
        $minX = $width;
        $maxX = 0;
        $minY = $height;
        $maxY = 0;
        $alphas = [];

        $strokeDist = (255 - $goldR) * 0.299 + (255 - $goldG) * 0.587 + (255 - $goldB) * 0.114;
        if ($strokeDist <= 0) {
            $strokeDist = 110.0;
        }

        for ($y = 0; $y < $height; $y++) {
            $alphas[$y] = [];
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($src, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $dist = (255 - $r) * 0.299 + (255 - $g) * 0.587 + (255 - $b) * 0.114;
                $alphaNorm = $dist / $strokeDist;
                if ($alphaNorm > 1.0) {
                    $alphaNorm = 1.0;
                }

                if ($alphaNorm < 0.05) {
                    $alphaNorm = 0.0;
                } else {
                    $alphaNorm = min(1.0, ($alphaNorm - 0.05) / 0.95);
                }

                $alphas[$y][$x] = $alphaNorm;

                if ($alphaNorm > 0.05) {
                    if ($x < $minX) {
                        $minX = $x;
                    }
                    if ($x > $maxX) {
                        $maxX = $x;
                    }
                    if ($y < $minY) {
                        $minY = $y;
                    }
                    if ($y > $maxY) {
                        $maxY = $y;
                    }
                }
            }
        }

        // If no content found, exit safely
        if ($minX >= $maxX || $minY >= $maxY) {
            imagedestroy($src);

            return;
        }

        // Tight crop with small 4px breathing room
        $padding = 4;
        $cropX = max(0, $minX - $padding);
        $cropY = max(0, $minY - $padding);
        $cropW = min($width - $cropX, ($maxX - $minX + 1) + ($padding * 2));
        $cropH = min($height - $cropY, ($maxY - $minY + 1) + ($padding * 2));

        // 1. Light Mode Logo (Warm elegant gold)
        $lightImg = imagecreatetruecolor($cropW, $cropH);
        imagealphablending($lightImg, false);
        imagesavealpha($lightImg, true);
        $transparent = imagecolorallocatealpha($lightImg, 0, 0, 0, 127);
        imagefilledrectangle($lightImg, 0, 0, $cropW, $cropH, $transparent);

        for ($y = 0; $y < $cropH; $y++) {
            $srcY = $cropY + $y;
            for ($x = 0; $x < $cropW; $x++) {
                $srcX = $cropX + $x;
                $a = $alphas[$srcY][$srcX];
                if ($a <= 0.001) {
                    continue;
                }
                $gdAlpha = (int) round((1.0 - $a) * 127);
                $color = imagecolorallocatealpha($lightImg, $goldR, $goldG, $goldB, $gdAlpha);
                imagesetpixel($lightImg, $x, $y, $color);
            }
        }

        // 2. Dark Mode Logo (Luminous radiant champagne gold #E8CFA2)
        $darkImg = imagecreatetruecolor($cropW, $cropH);
        imagealphablending($darkImg, false);
        imagesavealpha($darkImg, true);
        imagefilledrectangle($darkImg, 0, 0, $cropW, $cropH, $transparent);

        $darkGoldR = 232;
        $darkGoldG = 207;
        $darkGoldB = 162;

        for ($y = 0; $y < $cropH; $y++) {
            $srcY = $cropY + $y;
            for ($x = 0; $x < $cropW; $x++) {
                $srcX = $cropX + $x;
                $a = $alphas[$srcY][$srcX];
                if ($a <= 0.001) {
                    continue;
                }
                $gdAlpha = (int) round((1.0 - $a) * 127);
                $color = imagecolorallocatealpha($darkImg, $darkGoldR, $darkGoldG, $darkGoldB, $gdAlpha);
                imagesetpixel($darkImg, $x, $y, $color);
            }
        }

        imagepng($lightImg, $lightLogoPath, 9);
        imagepng($darkImg, $darkLogoPath, 9);

        imagedestroy($lightImg);
        imagedestroy($darkImg);
        imagedestroy($src);
    }
}
