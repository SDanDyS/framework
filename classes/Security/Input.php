<?php
    namespace Security;
    class Input
    {

        private $errors;

        public function requiredFields(array $data, bool $immediateExit = true) : void
        {
            $inputNames = array_keys($data);

            $method = '$_'.Url::getRequestMethod();

            if ($immediateExit)
            {
                foreach ($inputNames as $key => $value)
                {
                    if (!in_array($key, $$method))
                    {
                        return $this->errors = $value;
                        break;
                    }
                }
            } else
            {
                foreach ($inputNames as $key => $value)
                {
                    if (!in_array($key, $$method))
                    {
                        $this->errors[] = $value;
                    }
                }
            }
        }

        public function isAssocArray(array $arr)
        {
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }

        public function getErrors(bool $encode = false) : string|array
        {
            if($encode)
            {
                $storage = $this->errors;
                $this->errors = "";
                return json_encode($storage);              
            }

            $storage = $this->errors;
            $this->errors = "";
            return $storage;
        }

        public function empty()
        {

        }
    }
?>