<?php
	include_once("CacheClass.php");
		$cacheOBJ = new CacheClass();
		$cacheOBJ->setCacheServiceAdapter(new FileSystemAdapter());

		// $cacheOBJ->setData(2, 'Shubham Sahu');

		// $cacheOBJ->getData(3);

		// $cacheOBJ->deleteData(1);

		// $cacheOBJ->exportCache();

		// $cacheOBJ->importCache('');

?>