<?php
	/**
	AUthor - Shubham Sahu
	 * 
	 */
	class CacheClass{
	    protected $cache;

	    public function setCacheServiceAdapter(CacheServiceInterface $cache){
	        $this->cache = $cache;
	    }

	    public function setData($key, $value){
	        $this->cache->setData($key, $value);
	    }

	    public function getData($key, $value='Not Found'){
	        $this->cache->getData($key);
	    }

	    public function deleteData($key){
	        $this->cache->deleteData($key);
	    }

	    public function importCache($fileWithPath){
	        $this->cache->importCache($fileWithPath);
	    }

	    public function exportCache(){
	        $this->cache->exportCache();
	    }
	}

	class FileSystemClass{
		function __construct(){
			$file = 'cgcache.txt';
			if(!is_file($file)){  
			    $myfile = fopen("cgcache.txt", "w");
			    fclose($myfile);
			}
		}

		function setData($key, $value){			
			filesize('cgcache.txt') == 0 ? file_put_contents('cgcache.txt', json_encode(array())) : '';
			$fileData = json_decode(file_get_contents("cgcache.txt"), true);
			$keyP = array_search($key, array_column($fileData, 'key'));
			if(!empty($keyP) || $keyP === 0){
				$fileData[$keyP]['accessTime'] = time();
				$fileData[$keyP]['value'] = $value;
				file_put_contents('cgcache.txt', json_encode($fileData));
				echo "Key Already Exist";
			}else if(!empty($fileData) && count($fileData) > 99){
				array_multisort(array_column($fileData, 'accessTime'), SORT_DESC, $fileData);
				$fileData = array_slice($fileData, 0, 98);
				$payload = array('key' => $key, 'value' => $value, 'accessTime' => time());
				$fileData[] = $payload;
				file_put_contents('cgcache.txt', json_encode($fileData));
			}
		}

		function getData($key, $value = 'Not Found'){
			$fileData = json_decode(file_get_contents("cgcache.txt"), true);
			$keyP = array_search($key, array_column($fileData, 'key'));
			if(!empty($keyP) || $keyP === 0){
				$fileData[$keyP]['accessTime'] = time();
				file_put_contents('cgcache.txt', json_encode($fileData));
				print_r($fileData[$keyP]);
			}else{
				echo 'Key Not Found';			
			}
		}

		function deleteData($key){
			$fileData = json_decode(file_get_contents("cgcache.txt"), true);
			$keyP = array_search($key, array_column($fileData, 'key'));
			if(!empty($keyP) || $keyP === 0){
				unset($fileData[$keyP]);
				file_put_contents('cgcache.txt', json_encode($fileData));
				echo "Key ".$key." Deleted Successfully";
			}else{
				echo "Key Not Found";
			}
		}

		function importCache($fileWithPath){
			$fileData = json_decode(file_get_contents($fileWithPath), true);
			foreach ($fileData as $cache) {
				$this->setData($cache['key'], $cache['value']);
			}
		}

		function exportCache(){
			$filename = time().'-cache.txt';
			fopen($filename, "w");
			$fileData = file_get_contents("cgcache.txt");
			file_put_contents($filename, $fileData);
			echo "Data exported Successfully";
		}
	}
 
	// Simple Interface for each Adapter we create
	interface CacheServiceInterface{
	    public function setData($key, $value);
	    public function getData($key, $value = 'Not Found');
	    public function deleteData($key);
	    public function importCache($fileWithPath);
	    public function exportCache();
	}

	//FS Adapter
	class FileSystemAdapter implements CacheServiceInterface{
	    protected $fs;

	    public function __construct(){
	        $this->fs = new FileSystemClass();
	    }

	    public function setData($key, $value){
	        $this->fs->setData($key, $value);
	    }

	    public function getData($key, $value = 'Not Found'){
	        $this->fs->getData($key, $value);
	    }

	    public function deleteData($key){
	        $this->fs->deleteData($key);
	    }

	    public function importCache($fileWithPath){
	        $this->fs->importCache($fileWithPath);
	    }

	    public function exportCache(){
	        $this->fs->exportCache();
	    }
	}
?>