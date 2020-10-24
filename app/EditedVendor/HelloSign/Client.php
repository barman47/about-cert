<?php
namespace App\EditedVendor\HelloSign;
use HelloSign\Client as HelloSignClient;
use Storage;

class Client extends HelloSignClient{

    public function __construct($first, $last = null, $api_url = self::API_URL, $oauth_token_url = self::OAUTH_TOKEN_URL, $http_client_config = array())
    {
        parent::__construct($first, $last = null, $api_url = self::API_URL, $oauth_token_url = self::OAUTH_TOKEN_URL, $http_client_config = array());
    }

    /**
     * Retrieves a link or copy of the files associated with a signature request
     *
     * @param  string $request_id
     * @param  string $dest_path (optional) where should the file be saved. Will retrieve link if empty.
     * @param  string $type (optional) get the files as a single pdf or a zip of many. Links will always be pdfs.
     * @return string
     * @throws BaseException
     */
    public function getFiles($request_id, $dest_path = null, $type = null)
    {
        if ($dest_path) { // file stream
            $response = $this->rest->get(
                static::SIGNATURE_REQUEST_FILES_PATH . '/' . $request_id,
                $type ? array('file_type' => $type) : null
            );

            $this->checkResponse($response, false);
            file_put_contents($dest_path, $response);
        } else { // link
            $response = $this->rest->get(
                static::SIGNATURE_REQUEST_FILES_PATH . '/' . $request_id,
                array('get_url' => true)
            );

            return new FileResponse($response);
        }

        return $response;
    }//end method getFiles
}//end class Client
