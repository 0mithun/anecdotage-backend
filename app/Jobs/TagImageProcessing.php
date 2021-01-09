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
            $client = new Client();

            $url = 'https://en.wikipedia.org/wiki/' . $this->tag->name;
            $crawler = $client->request('GET', $url);

            // $meta = $crawler->filter( 'meta' );

            // $meta->each( function ( $node ) {
            //     $property = $node->extract( ['property'] )[0];
            //     if ( $property != '' ) {
            //         $full_image_link = $node->extract( ['content'] )[0];
            //         $fileExtension = explode( '.', $full_image_link );
            //         $fileExtension = array_pop( $fileExtension );
            //         $fileName = strtolower( $this->tag->name );

            //         $fullFileName = $fileName . '.' . $fileExtension;
            //         $image_path = strtolower( 'download/tag/' . $fullFileName );
            //         $fullPath = 'public/' . $image_path;

            //         $this->file_download_curl( $fullPath, $full_image_link );
            //         $this->saveInfo( $image_path );

            //     }

            // } );


            $this->scrapeWithKeyword();
        }
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

        $authorText = '';
        $licenseText = '';
        $descriptionText = '';
        $shopText =  "http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={$this->tag->name}&linkCode=ur2&tag=anecdotagecom-20";

        $image_page = $client->request('GET', $image_page_url);

        if ($image_page->filter('.fullImageLink a')->count() > 0) {
            $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            $full_image_link = 'https://' . $full_image_link;
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
                }
            }
            $author = $image_page->filter('td#fileinfotpl_aut');
            if ($author->count() > 0) {
                $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
                $newAuthor = $newAuthor->filter('a');
                if ($newAuthor->count() > 0) {
                    $authorText =  $newAuthor->first()->text();
                }
            }
            $fullDescriptionText = sprintf("%s %s %s %s", $descriptionText, $authorText, $licenseText, $shopText);
            $data = [
                'photo' =>  $full_image_link,
                'description' =>  $fullDescriptionText,
            ];

            $this->saveInfo($data);
        }
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

    // public function saveInfo($image_path)
    // {
    //     $tag = Tags::where('id', $this->tag->id)->first();
    //     $tag->photo = $image_path;
    //     $tag->save();
    // }

    public function saveInfo($data)
    {
        $this->tag->update($data);
    }
}
