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
		public $configs;

		function __construct(){
 			$this->configs = include('config.php');

			if(!is_file($this->configs['cacheFileName'])){  
			    $myfile = fopen($this->configs['cacheFileName'], "w");
			    fclose($myfile);
			}
		}

		function setData($key, $value){			
			filesize($this->configs['cacheFileName']) == 0 ? file_put_contents($this->configs['cacheFileName'], json_encode(array())) : '';
			$fileData = json_decode(file_get_contents($this->configs['cacheFileName']), true);
			$keyP = array_search($key, array_column($fileData, 'key'));


			$keyP = array_search($key, array_column($fileData, 'key'));
			if(!empty($keyP) || $keyP === 0){
				$fileData[$keyP]['accessTime'] = time();
				$fileData[$keyP]['value'] = $value;
				echo "Key Already Exist";
			}else{
				if(!empty($fileData) && count($fileData) > $this->configs['fileSize']){
					array_multisort(array_column($fileData, 'accessTime'), SORT_DESC, $fileData);
					$fileData = array_slice($fileData, 0, $this->configs['fileSize']-1);
				}
				$payload = array('key' => $key, 'value' => $value, 'accessTime' => time());
				$fileData[] = $payload;
			}
			file_put_contents($this->configs['cacheFileName'], json_encode($fileData));
		}

		function getData($key, $value = 'Not Found'){
			$fileData = json_decode(file_get_contents($this->configs['cacheFileName']), true);
			$keyP = array_search($key, array_column($fileData, 'key'));
			if(!empty($keyP) || $keyP === 0){
				$fileData[$keyP]['accessTime'] = time();
				file_put_contents($this->configs['cacheFileName'], json_encode($fileData));
				print_r($fileData[$keyP]);
			}else{
				echo 'Key Not Found';
			}
		}

		function deleteData($key){
			$fileData = json_decode(file_get_contents($this->configs['cacheFileName']), true);
			$keyP = array_search($key, array_column($fileData, 'key'));
			if(!empty($keyP) || $keyP === 0){
				unset($fileData[$keyP]);
				file_put_contents($this->configs['cacheFileName'], json_encode($fileData));
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
			$fileData = file_get_contents($this->configs['cacheFileName']);
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