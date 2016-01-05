<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessImage extends Command implements ShouldQueue {
  
  public $media = null;

	use InteractsWithQueue, SerializesModels;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(\App\Models\Media $media)
	{
    \Log::info('CONSTRUCT Process Image '.$media->id.': '.microtime());
		$this->media = $media;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
  	$media = $this->media;
  	
    \Log::info('ATTEMPT Process Image '.$media->id.': '.microtime());
    
    if ($media) {
      \Log::info('START Process Image '.$media->id.': '.microtime());
      
      if ( class_exists("Imagick") ) {
        $sizes = \Config::get('media.sizes');
        
        $createdSizes = ['o'];
        
        $img = new \Imagick($media->filePath('o'));
        $imgProps = $img->getImageGeometry();
        
        $dimensions = [
          'o' => [
            'width' => $imgProps['width'],
            'height' => $imgProps['height'],
          ],
        ];
        foreach ($sizes as $size => $settings) {
          \Log::info('START Size '.$size.': '.microtime());
          
          $img = new \Imagick($media->filePath('o'));
          $imgProps = $img->getImageGeometry();
          
          if ($imgProps['width'] >= $settings['length'] || $imgProps['height'] >= $settings['length']) {
            
            $imgRatio = $imgProps['width'] / $imgProps['height'];

            if ($settings['aspectRatio'] != 'source') {
              list($aspectWidth, $aspectHeight) = explode(':', $settings['aspectRatio']);
              $cropRatio = $aspectWidth / $aspectHeight;
              
              if ($cropRatio == 1) {
                $newWidth = $newHeight = $settings['length'];
              } else if ($cropRatio > 1) {
                $newWidth = $settings['length'];
                $newHeight = $settings['length'] / $cropRatio;
              } else {
                $newWidth = $settings['length'] / $cropRatio;
                $newHeight = $settings['length'];
              }
              
              if ($imgRatio >= $cropRatio) {
                $img->resizeImage(999999, $newHeight, \Imagick::FILTER_BOX, 1, TRUE);
              } else {
                $img->resizeImage($newWidth, 999999, \Imagick::FILTER_BOX, 1, TRUE);
              }
              $imgProps = $img->getImageGeometry();
              
              $newX = ($imgProps['width'] - $newWidth) / 2;
              $newY = ($imgProps['height'] - $newHeight) / 2;

              $img->cropImage($newWidth, $newHeight, $newX, $newY);
              $img->setImagePage(0, 0, 0, 0); // Fixes a bug(?) where cropping leaves behind blank canvas
            } else {
              $img->resizeImage($settings['length'], $settings['length'], \Imagick::FILTER_BOX, 1, TRUE);
            }
            
            if ($img->writeImage($media->filePath($size))) {
              $createdSizes[] = $size;
              $dimensions[$size] = [
                'width' => $imgProps['width'],
                'height' => $imgProps['height'],
              ];

              \Log::info('Saved Size '.$size.': '.microtime());
            } else {
              \Log::info('Error Saving Size '.$size.': '.microtime());
            }
            \Log::info('END Size '.$size.': '.microtime());
          }
        }
        
        $media->dimensions = $dimensions;
        $media->processed = 1;
        $media->save();
      }

      \Log::info('END Process Image '.$media->id.': '.microtime());
    }

	}

}
