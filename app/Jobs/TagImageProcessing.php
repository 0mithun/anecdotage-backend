<?php

namespace App\Jobs;

use Goutte\Client;
use App\Models\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TagImageProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tag;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->tag->name != null) {
            $this->scrapeWithKeyword();
        }
    }



    /**
     * Undocumented function
     *
     * @return void
     */
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
        if(isset($image_page_url)){
            $this->scrpeImagePageUrl($image_page_url);
        }
    }


    /**
     * Undocumented function
     *
     * @param String $image_page_url
     * @return void
     */
    public function scrpeImagePageUrl($image_page_url)
    {
        $client = new Client();

        $authorText = '';
        $htmlLicense = '';
        $descriptionText = '';
        // $shopText = '<a class="btn btn-sm btn-secondary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=' . $this->tag->name . '&linkCode=ur2&tag=anecdotage01-20">Buy it here</a>';
        $image_page = $client->request('GET', $image_page_url);

         if($image_page->filter('.mw-filepage-resolutioninfo a')->count() > 0){
            $full_image_link =  $image_page->filter('.mw-filepage-resolutioninfo a')->first()->extract(['href'])[0];
            $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            $full_image_link = 'https://' . $full_image_link;
            $full_image_link =  str_replace("//https:", '//', $full_image_link);

            dump($full_image_link);
        }
        elseif ($image_page->filter('.fullImageLink a')->count() > 0) {
            $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            $full_image_link = 'https://' . $full_image_link;
            $full_image_link =  str_replace("//https:", '//', $full_image_link);
            dump('default resolution');
        }

        if (isset($full_image_link)) {

            $description = $image_page->filter('td.description');
            if ($description->count() > 0) {
                // $description =  $description->first()->text();
                // $descriptionText = str_replace('English: ', '', $description);

                $english = $description->filter('div.en');
                if($english->count()>0){
                    $description =  $english->first()->text();
                    dump('English Description', $description);
                }else{
                    $description =  $description->first()->text();
                }

                $descriptionText = str_replace('English: ', '', $description);
            }
            $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            if ($license->count() > 0) {
                $saLicenseType = [
                    'CC BY-SA 1.0',
                    'CC BY-SA 1.5',
                    'CC BY-SA 2.0',
                    'CC BY-SA 2.5',
                    'CC BY-SA 3.0',
                    'CC BY-SA 4.0',
                ];
                $nonSaLicenseType = [
                    'CC BY 1.0',
                    'CC BY 1.5',
                    'CC BY 2.0',
                    'CC BY 2.5',
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
                   $htmlLicense = $this->checkLicense($image_page);
                }
            }else{
                $htmlLicense = $this->checkLicense($image_page);
            }
            $author = $image_page->filter('td#fileinfotpl_aut');

            if ($author->count() > 0) {
                 $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
                // $newAuthorAnchor = $newAuthor->filter('a');

                /*

                    if ($newAuthorAnchor->count() > 0) {
                        $authorText = $newAuthorAnchor->first()->text();
                    }else{
                        $authorText = $newAuthor->first()->text();
                    }


                */
                $newAuthorAnchor = $newAuthor->filter('a.extiw');
                if ($newAuthorAnchor->count() > 0) {
                    $authorText = $newAuthorAnchor->first()->text();
                }else{
                   $authorText = $newAuthor->first()->text();
                }

                // $authorText = $newAuthor->first()->text();
                if($authorText != null || $authorText !=''){
                    $authorText = "Credit: ".$authorText;
                }
            }

             // $authorText = $newAuthor->first()->text();
            if($htmlLicense != null || $htmlLicense !=''){
                $htmlLicense = "($htmlLicense)";
            }

            // $fullDescriptionText = sprintf("%s %s %s %s", $descriptionText, $authorText, $htmlLicense, $shopText);
            $fullDescriptionText = sprintf("%s Credit: %s (%s)", $descriptionText, $authorText, $htmlLicense);
            $data = [
                'photo' =>  $full_image_link,
                'description' =>  $fullDescriptionText,
            ];

            $this->saveInfo($data);
        }
    }

    /**
     * Undocumented function
     *
     * @param Array $data
     * @return void
     */
    public function saveInfo($data)
    {
        $this->tag->update($data);
    }


    /**
     * Undocumented function
     *
     * @param [type] $image_page
     * @return void
     */
    public function checkLicense($image_page){
        dump('inside check license');
        $text =    $image_page->text();
         $licenseText = '';
         $htmlLicense = '';
            $saLicenseType = [
                'CC BY-SA 1.0',
                'CC BY-SA 1.5',
                'CC BY-SA 2.0',
                'CC BY-SA 2.5',
                'CC BY-SA 3.0',
                'CC BY-SA 4.0',

                'CC-BY-SA 1.0',
                'CC-BY-SA 1.5',
                'CC-BY-SA 2.0',
                'CC-BY-SA 2.5',
                'CC-BY-SA 3.0',
                'CC-BY-SA 4.0',
            ];
            $nonSaLicenseType = [
                'CC BY 1.0',
                'CC BY 1.5',
                'CC BY 2.0',
                'CC BY 2.5',
                'CC BY 3.0',
                'CC BY 4.0',

                'CC-BY-1.0',
                'CC-BY-1.5',
                'CC-BY-2.0',
                'CC-BY-2.5',
                'CC-BY-3.0',
                'CC-BY-4.0',
            ];
            $matched = false;

            foreach ($saLicenseType as $license) {
                $pattern = "/$license/i";
                if(preg_match($pattern, $text)){
                    $licenseText = $license;
                    $matched = true;
                    break;
                }
            }

            if($matched == false){
                foreach ($nonSaLicenseType as $license) {
                    $pattern = "/$license/i";
                    if(preg_match($pattern ,$text)){
                       $licenseText = $license;
                        $matched = true;
                        break;
                    }
                }
            }else{
                if(preg_match("/fair use/i" ,$text)){
                    $licenseText = 'Fair use';
                }else if(preg_match('/public domain/i', $text)){
                    $licenseText = 'Public domain';
                }
            }

            if ($licenseText == 'Public domain') {
                $htmlLicense = 'Public domain';
            }elseif($licenseText == 'Fair use'){
                $licenseText = 'Fair use';
            } else if (in_array($licenseText, $saLicenseType)) {
                if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
                    $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/' . $matches[0] . '">' . $licenseText . '</a>';
                }
            } else if (in_array($licenseText, $nonSaLicenseType)) {
                if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
                    $htmlLicense = '<a href="https://creativecommons.org/licenses/by/' . $matches[0] . '">' . $licenseText . '</a>';
                }
            }


       return $htmlLicense;
    }

}
