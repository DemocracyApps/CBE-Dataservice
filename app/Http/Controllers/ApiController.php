<?php
namespace CBEDataService\Http\Controllers;

use Illuminate\Http\Response;

class APIController extends Controller {

    /**
     * @var integer
     */
    protected $statusCode = Response::HTTP_OK;

    protected function setStatusAndRespond($resp) {
        if (is_array($resp) && array_key_exists('status_code',$resp)) {
            $this->setStatusCode($resp['status_code']);
            return $this->respond($resp);
        }
        else {
            return $resp;
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }


    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message)
    {
        return $this->respond([
            'message'     => $message,
            'status_code' => $this->getStatusCode(),
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode()
            ],
            'data' => null
        ]);
    }

    public function respondNotFound ($message = 'Not Found')
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)->respondWithError($message);
    }

    public function respondFormatError ($message = "Bad format")
    {
        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)->respondWithError($message);
    }

    public function respondInternalError ($message = 'Internal Error')
    {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    public function respondFailedValidation($message = 'Failed validation')
    {
        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    public function respondOK($message = 'Operation succeeded', $data = null)
    {
        return $this->setStatusCode(Response::HTTP_OK)->respond([
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data
        ]);
    }

    public function respondCreated($message = 'Successfully created', $data)
    {
        return $this->setStatusCode(Response::HTTP_CREATED)->respond([
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data
        ]);
    }

    public function respondIndex($message = 'Success', $data, $transform = null, $params = null)
    {
        if ($transform != null) {
            if ($params == null) $params = array();
            $ndata = array();
            foreach ($data as $item) {
                $ndata[] = call_user_func_array([$transform,'transform'], [$item, $params]);
            }
            $data = $ndata;
        }
        return $this->setStatusCode(Response::HTTP_OK)->respond([
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data
        ]);
    }
    public function respondItem($message = 'Success', $data, $transform = null, $params = null)
    {
        if ($transform != null) {
            if ($params == null) $params = array();
            $data = call_user_func_array([$transform,'transform'], [$data, $params]);
        }
        return $this->setStatusCode(Response::HTTP_OK)->respond([
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data
        ]);
    }

}
