<?php
    namespace Security;
    class Input
    {

        private $errors = [];
        private $method;

        public function __construct()
        {
            $this->method = Url::getRequestVariable();
        }

        public function requiredFields(array|string $data, bool $immediateExit = true) : void
        {
            $this->initializeRequest($data, $immediateExit);
        }

        public function assocData(array $inputNames, bool $immediateExit = true) : void
        {
            if ($immediateExit)
            {
                foreach ($inputNames as $key => $value)
                {
                    if (!array_key_exists($key, $this->method))
                    {
                        $this->errors = $value;
                        break;
                    }
                }
            } else
            {
                foreach ($inputNames as $key => $value)
                {
                    if (!array_key_exists($key, $this->method))
                    {
                        $this->errors[] = $value;
                    }
                }
            }
        }

        public function indexData(array $inputNames, bool $immediateExit = true) : void
        {
            if ($immediateExit)
            {
                foreach ($inputNames as $value)
                {
                    if (!array_key_exists($value, $this->method))
                    {
                        $this->errors = $value;
                        break;
                    }
                }
            } else
            {
                foreach ($inputNames as $value)
                {
                    if (!array_key_exists($value, $this->method))
                    {
                        $this->errors[] = $value;
                    }
                }
            }            
        }

        public function isAssocArray(array $arr) : bool
        {
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }

        public function getErrors(bool $encode = false) : string|array
        {
            if($encode)
            {
                $storage = $this->errors;
                $this->errors = [];
                return json_encode($storage);              
            }

            $storage = $this->errors;
            $this->errors = [];
            return $storage;
        }

        public function empty(array|string $data, bool $immediateExit = true) : void
        {
            $this->initializeRequest($data, $immediateExit);
        }

        private function initializeRequest(array|string $data, bool $immediateExit = true) : void
        {
            if (is_string($data))
            {
                $dataStorage = $data;
                $data = [];
                $data[] = $dataStorage;
            }

            if ($this->isAssocArray($data))
            {
                $this->assocData($data, $immediateExit);
            } else
            {
                $this->indexData($data, $immediateExit);
            }
        }
    }
?>