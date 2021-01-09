<?php

namespace App\Jobs;

use App\Thread;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WikiImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $wikiUrl;
    protected $thread;
    protected $isDelete;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wikiUrl, $thread, $isDelete)
    {
        $this->wikiUrl = $wikiUrl;
        $this->thread = $thread;
        $this->isDelete = $isDelete;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->wikiUrl != null) {
            // $client = new Client();
            // $crawler = $client->request('GET', $this->wikiUrl);

            // // $html =  $crawler->filter('table.infobox a.image img')->first();
            // $anchor = $crawler->filter('table.infobox a.image')->first();

            // if (count($anchor) > 0) {
            //     $href = $anchor->extract(['href'])[0];
            //     $image_page_url = 'https://en.wikipedia.org' . $href;
            //     $image_page = $client->request('GET', $image_page_url);

            //     $full_image_link = $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            //     $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            //     $full_image_link = 'https://' . $full_image_link;

            //     $description = $image_page->filter('td.description');
            //     $description = ($description->count() > 0) ? $description->first()->text() : "";

            //     $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            //     $license = ($license->count() > 0) ? $license->first()->text() : "";

            //     $description = str_replace('English: ', '', $description);
            //     $description = $description . "(" . $license . ")";

            //     if ($full_image_link != '') {
            //         $fileExtension = explode('.', $full_image_link);
            //         $fileExtension = array_pop($fileExtension);
            //         $fileName = md5(time() . uniqid());
            //         $fullFileName = $fileName . '.' . $fileExtension;
            //         $image_path = 'download/wikipedia/' . $fullFileName;
            //         $fullPath = 'public/' . $image_path;

            //         $this->file_download_curl($fullPath, $full_image_link);
            //         $this->saveInfo($image_page_url, $full_image_link, $description, $image_path);
            //         if ($this->isDelete) {
            //             $this->deleteImage($this->thread->wiki_image_path);
            //         }
            //     }
            // }

            $this->scrapeWithKeyword($this->wikiUrl);
        }
    }

    public function scrapeWithKeyword($newUrl)
    {
        // $keyword = ucwords($keyword);
        // $keyword = str_replace(' ', '_', $keyword);
        // $newUrl = "https://en.wikipedia.org/wiki" . '/' . $keyword;

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
        if (isset($image_page_url)) {
            $this->scrpeImagePageUrl($image_page_url);
        }
    }


    public function scrpeImagePageUrl($image_page_url)
    {
        $client = new Client();
        $licenseText = '';
        $descriptionText = '';

        $image_page = $client->request('GET', $image_page_url);

        if ($image_page->filter('.fullImageLink a')->count() > 0) {
            $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            $full_image_link = 'https://' . $full_image_link;
            $full_image_link =  str_replace("//https:", '//', $full_image_link);
        }

        if (isset($full_image_link)) {
            $description = $image_page->filter('div.description');
            if ($description->count() > 0) {
                $description =  $description->first()->text();
                $descriptionText = str_replace('English: ', '', $description);
            }
            $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            if ($license->count() > 0) {
                $acceptedLicenses = [
                    'CC BY-SA 1.0',
                    'CC BY-SA 1.5',
                    'CC BY 1.0',
                    'CC BY 1.5',
                    'CC BY-SA 2.5',
                    'CC BY 2.0 ',
                    'CC BY 2.5 ',
                    'CC BY-SA 3.0',
                    'CC BY 3.0',
                    'Public domain',
                    'CC BY-SA 4.0',
                    'CC BY 4.0',
                ];

                if (in_array($license->first()->text(), $acceptedLicenses)) {
                    $licenseText = $license->first()->text();
                   
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
                    
                    if( $licenseText == 'Public domain'){
                        $htmlLicense = '(Public domain)';
                    }
                    else if ( in_array( $licenseText, $saLicenseType ) ) {
                        if( \preg_match('&(\d)\.?\d?&',$licenseText, $matches)){

                            $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$matches[0].'">('.$licenseText.')</a>';
                        }
                    }else if ( in_array( $licenseText, $nonSaLicenseType ) ) {
                        if(\preg_match('&(\d)\.?\d?&',$licenseText, $matches)){

                            $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$matches[0].'">('.$licenseText.')</a>';
                        }
                    } 
                }
            }

            $pixelColor = $this->getImageColorAttribute($full_image_link);

            $fullDescriptionText = sprintf( '%s %s', $descriptionText,  $htmlLicense  );

            $data = [
                'wiki_image_page_url' => $image_page_url,
                'wiki_image_url' => $full_image_link,
                'wiki_image_path' => $full_image_link,
                'wiki_image_path_pixel_color' => $pixelColor ?? '',
                'wiki_image_description' => $fullDescriptionText,
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

    public function file_download_curl($fullPath, $full_image_link)
    {
        $parts = explode('/', $fullPath);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen($fullPath, 'wb');
        $ch = curl_init($full_image_link);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        fclose($fp);
    }


    public function deleteImage($url)
    {
        if (\File::exists(public_path() . '/' . $url)) {
            \File::delete(public_path() . '/' . $url);
        } else {
            echo ('File does not exists.');
        }
    }
}
