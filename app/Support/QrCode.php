<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Generation de QR codes en SVG (aucune extension image requise).
 */
class QrCode
{
    /**
     * @param  int  $size  Cote du QR en pixels.
     * @param  int  $margin Marge blanche (« quiet zone »), en modules. En
     *                      dessous de 4, certains lecteurs echouent.
     */
    public static function svg(string $content, int $size = 320, int $margin = 4): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, $margin, null, null, Fill::uniformColor(
                new \BaconQrCode\Renderer\Color\Rgb(255, 255, 255),
                new \BaconQrCode\Renderer\Color\Rgb(18, 18, 18)
            )),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($content);
    }

    /**
     * SVG encode en data URI : utilisable directement dans un src d'image,
     * ce qui evite une requete supplementaire a l'impression.
     */
    public static function dataUri(string $content, int $size = 320): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(static::svg($content, $size));
    }
}
