<?php

/**
 * A2W_Requests_Response class
 * 
 * wrapper object for a Requests_Response for standardisation.
 */
class A2W_Requests_Response {

    protected $response;

    /**
     * Constructor.
     *
     * @param Requests_Response $response HTTP response.
     */
    public function __construct(Requests_Response $response) {
        $this->response = $response;
    }

    /**
     * Retrieves the response object for the request.
     * 
     * @return Requests_Response HTTP response.
     */
    public function get_response_object() {
        return $this->response;
    }

    /**
     * Retrieves headers associated with the response.
     *
     * @return array Map of header name to header value.
     */
    public function get_headers() {
        // Ensure headers remain case-insensitive
        $converted = new Requests_Utility_CaseInsensitiveDictionary();

        foreach ($this->response->headers->getAll() as $key => $value) {
            if (count($value) === 1) {
                $converted[$key] = $value[0];
            } else {
                $converted[$key] = $value;
            }
        }

        return $converted;
    }

    /**
     * Sets all header values.

     * @param array $headers Map of header name to header value.
     */
    public function set_headers($headers) {
        $this->response->headers = new Requests_Response_Headers($headers);
    }

    /**
     * Sets a single HTTP header.
     *
     * @param string $key     Header name.
     * @param string $value   Header value.
     * @param bool   $replace Optional. Whether to replace an existing header of the same name.
     *                        Default true.
     */
    public function header($key, $value, $replace = true) {
        if ($replace) {
            unset($this->response->headers[$key]);
        }

        $this->response->headers[$key] = $value;
    }

    /**
     * Retrieves the HTTP return code for the response.
     *
     * @return int The 3-digit HTTP status code.
     */
    public function get_status() {
        return $this->response->status_code;
    }

    /**
     * Sets the 3-digit HTTP status code.
     *
     * @param int $code HTTP status.
     */
    public function set_status($code) {
        $this->response->status_code = absint($code);
    }

    /**
     * Retrieves the response data.
     *
     * @return mixed Response data.
     */
    public function get_data() {
        return $this->response->body;
    }

    /**
     * Sets the response data.
     *
     * @param mixed $data Response data.
     */
    public function set_data($data) {
        $this->response->body = $data;
    }

    /**
     * Retrieves cookies from the response.
     *
     */
    public function get_cookies() {
        return $this->response->cookies;
    }

    /**
     * Converts the object to array
     *
     * @return array
     */
    public function to_array() {
        return array(
            'headers' => $this->get_headers(),
            'body' => $this->get_data(),
            'response' => array(
                'code' => $this->get_status(),
                'message' => get_status_header_desc($this->get_status()),
            ),
            'cookies' => $this->get_cookies()
        );
    }

}
