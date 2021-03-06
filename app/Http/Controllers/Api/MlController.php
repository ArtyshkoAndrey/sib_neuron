<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\ModelManager;
use Illuminate\Http\Request;
use App\Image2Ml;
use App\ImageParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Photo;
use App\Albums;
use App\Video;

class MlController extends Controller
{

    public function train(Request $request)
    {
        $response = array(
            'msg' => '',
            'im' =>  '',
        );
        //Заглушка редирект назад с ошибкой
        // return bac\k()->with('status', 'Обучение приостоновлено!! Доступ откроется в 3 часа по МСК');
        if ($request->isMethod('post')) {

            if (file_exists(public_path() . '/neuron/model.data') && $request->options != 'error') {

                $file = $request->image;
                $label = array((string)$request->label);

                $im = new Image2Ml($file);
                $trainedData = $im->grayScalePixels();
                $modelManager = new ModelManager();
                $classifier = $modelManager->restoreFromFile(public_path() . '/neuron/model.data');
                $classifier->train($trainedData, $label);
                $modelManager->saveToFile($classifier, public_path() . '/neuron/model.data');
                $response['msg'] = "Тест траин пройден";

            } else
                $response['msg'] = "Тест траин не пройден";
        }
        // if(!rand(0,1))
            $im = new ImageParser('https://pixabay.com/api/?key=10542644-549c54b5d387dd41892ea2b24&q=people&image_type=photo&cat=people&order=latest&per_page=50&page='.rand(1,6));
        // else
        //    $im = new ImageParser('https://pixabay.com/api/?key=10542644-549c54b5d387dd41892ea2b24&q=dog&image_type=photo&order=latest&per_page=50&page='.rand(1,6));
        $im = $im->setIm();
        $response['im'] = $im;
        return response()->json($response);
    }

    public function createAlbums(Request $request) {
        $id = (integer)$request->id;
        $photos = Photo::where('user_id', $id)->get();
        $i=0;
        $modelManager = new ModelManager();
        $classifier = $modelManager->restoreFromFile(public_path() . '/neuron/model.data');
        foreach ($photos as $photo) {
            $im = new Image2Ml($photo['th_url']);
            $Data = $im->grayScalePixels();
            $label = $classifier->predictProbability($Data);
            if($label[0]['people'] > 0.8) {
                $newAlbum = new Albums();
                $newAlbum->photo_id = $photo->id;
                $newAlbum->user_id = $id;
                $newAlbum->category_id = 2;
                $newAlbum->save();
            } else if($label[0]['dog'] > 0.7) {
                $newAlbum = new Albums();
                $newAlbum->photo_id = $photo->id;
                $newAlbum->user_id = $id;
                $newAlbum->category_id = 1;
                $newAlbum->save();
            }
        }
    }
    public function create_video(Request $request) {
    	$ffmpeg = "";
			$cat_id = $request->category_id;
			$id = $request->id;
			$myfile = fopen(public_path()."/people/newfile.txt", "w");

			$photos = Albums::where('user_id', $id)->where('category_id', $cat_id)->get();
			$i=0;
			$txt="";
			foreach ($photos as $photo) {
				$p = Photo::where('id', $photo->photo_id)->first();
				$k = strval($i);
				while (strlen($k) < 3) {
					$k = "0" . $k;
				}
				copy($p->url,public_path()."/people/". $k .".jpg");
				$i++;
			}
			fwrite($myfile, $txt);
			fclose($myfile);
			$name = rand(0,1111);

			exec('ffmpeg -r 1 -start_number 0 -i "/var/www/html/public/people/%3d.jpg" -c:v libx264 -vf "fps=30,format=yuv420p" "/var/www/html/public/video/'.$name.'.mp4"');
			exec('rm /var/www/html/public/people/*');

			$video = new Video();
			$video->user_id = $id;
			$video->src = $name;
			$video->save();
		}
}


        // $modelManager = new ModelManager();
        // $classifier = $modelManager->restoreFromFile(public_path() . '/neuron/data');
        // $binP = base_path() ."/vendor/php-ai/php-ml/bin/libsvm";
        // $varP = base_path() ."/vendor/php-ai/php-ml/var";
        // $classifier->setBinPath($binP);
        // $classifier->setVarPath($varP);
        // $modelManager->saveToFile($classifier, public_path() . '/neuron/model.data');
