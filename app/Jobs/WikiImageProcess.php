<?php

namespace App\Jobs;

use Goutte\Client;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WikiImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $keyword;
    protected $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($keyword, Thread $thread)
    {
        $this->keyword = $keyword;
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->scrapeWithKeyword($this->keyword);
    }

    public function scrapeWithKeyword()
    {
        $keyword = $this->tag->name;

        $keyword = ucwords($keyword);
        $keyword = str_replace(' ', '_', $keyword);
        $newUrl = "https://en.wikipedia.org/wiki" . '/' . $keyword;

        $client = new Client();
        $crawler = $client->request('GET', $newUrl);

        $infobox = $crawler->filter('table.infobox a.image')->first();

        if (count($infobox)) {
            $href = $infobox->extract(['href'])[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
        } else {
            $thumbinner =  $crawler->filter('div.thumbinner a.image')->first();
            if (count($thumbinner) > 0) {
                $href = $thumbinner->extract(['href'])[0];
                $image_page_url = 'https://en.wikipedia.org' . $href;
            }
        }
        $this->scrpeImagePageUrl($image_page_url);
    }


    public function scrpeImagePageUrl($image_page_url)
    {
        $client = new Client();
        $licenseText = '';
        $htmlLicense = '';
        $descriptionText = '';

        $image_page = $client->request('GET', $image_page_url);

        if ($image_page->filter('span.mw-filepage-other-resolutions')->count() > 0) {
            $full_image_link = $image_page->filter('span.mw-filepage-other-resolutions a')->first()->extract(['href'])[0];
        } else

        if ($image_page->filter('.fullImageLink a')->count() > 0) {
            $full_image_link = $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
        }

        $full_image_link = str_replace('//upload', 'upload', $full_image_link);
        $full_image_link = 'https://' . $full_image_link;

        if (isset($full_image_link)) {
            $description = $image_page->filter('div.description');
            if ($description->count() > 0) {
                $description =  $description->first()->text();
                $descriptionText = str_replace('English: ', '', $description);
            }
            $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            if ($license->count() > 0) {
                $saLicenseType = [
                    'CC BY-SA 1.0',
                    'CC BY-SA 1.5',
                    'CC BY-SA 2.5',
                    'CC BY-SA 3.0',
                    'CC BY-SA 4.0',
                ];
                $nonSaLicenseType = [
                    'CC BY 1.0',
                    'CC BY 1.5',
                    'CC BY 2.0 ',
                    'CC BY 2.5 ',
                    'CC BY 3.0',
                    'CC BY 4.0',
                ];

                $licenseText = $license->first()->text();
                if ($licenseText == 'Public domain') {
                    $htmlLicense = 'Public domain';
                } else if (in_array($licenseText, $saLicenseType)) {
                    if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/' . $matches[0] . '">' . $licenseText . '</a>';
                    }
                } else if (in_array($licenseText, $nonSaLicenseType)) {
                    if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by/' . $matches[0] . '">' . $licenseText . '</a>';
                    }
                }

                if ($htmlLicense != '') {
                    \dump($htmlLicense);
                } else {
                    \dump('other license');
                }
            }

            $author = $image_page->filter('td#fileinfotpl_aut');

            if ($author->count() > 0) {
                $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
                $newAuthor = $newAuthor->filter('a');

                if ($newAuthor->count() > 0) {
                    $authorText = $newAuthor->first()->text();
                }
            }

            $fullDescriptionText = sprintf('%s %s %s', $descriptionText, $authorText, $htmlLicense);
            $pixelColor = $this->getImageColorAttribute($full_image_link);
            $data = [
                'image_path' => $full_image_link,
                'image_path_pixel_color' => $pixelColor ?? '',
                'image_description' => $fullDescriptionText

            ];

            $this->saveInfo($data);
        }
    }

    public function getImageColorAttribute($image_path)
    {
        if ($image_path != '') {
            $splitName = explode('.', $image_path);
            $extension = strtolower(array_pop($splitName));

            if ($extension == 'jpg') {
                $im = imagecreatefromjpeg($image_path);
            }
            if ($extension == 'jpeg') {
                $im = imagecreatefromjpeg($image_path);
            } else if ($extension == 'png') {
                $im = imagecreatefrompng($image_path);
            } else if ($extension == 'gif') {
                $im = imagecreatefromgif($image_path);
            }

            if (isset($im)) {
                $rgb = imagecolorat($im, 0, 0);
                $colors = imagecolorsforindex($im, $rgb);
                array_pop($colors);
                array_push($colors, 1);
                $rgbaString = join(', ', $colors);

                return $rgbaString;
            }
        }
        return false;
    }

    public function saveInfo($data)
    {
        $this->thread->update($data);
    }
}
