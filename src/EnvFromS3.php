<?php

require 'vendor/autoload.php';

namespace Activerules\EnvFromS3;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class EnvFromS3
{

    /**
     * Create a new EnvFromS3 instance
     */
    public function __construct($bucket, $key, $region, $version)
    {
        $envSettings = $this->fetchFromS3($bucket, $key);
        
        if($envSettings) {
            $this->processSettings($envSettings);
        }
    }
    
    private function processSettings($envSettings) {
        foreach($envSettings as $name => $value) {
            $this->setEnv($name, $value);
        }
    }
    
    private function fetchFromS3($bucket, $key, $region, $version='lastest') {
        
        $s3 = new S3Client([
            'version' => $version,
            'region'  => $region
        ]);
        
        try {
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key'    => $key
            ]);
            return $result;
        } catch (S3Exception $e) {
            return false;
        }
    }   
    
    private function setEnv($name, $value) {
        putenv("$name=$value");
    }
}