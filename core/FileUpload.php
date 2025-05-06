<?php
namespace Core;

class FileUpload
{
    public $file;
    public $name;
    public $size;
    public $type;
    public $tmp;
    public $error;
    public $extension;

    public $uploadErrors = [];

    public $randomFileName = false;

    public $newFileName = null;

    public $path;

    private $filesErrorMessages = [
        0 => "There is no error, the file uploaded with success",
        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        3 => "The uploaded file was only partially uploaded",
        4 => "No file was uploaded",
        6 => "Missing a temporary folder",
    ];

    public function __construct(string $file)
    {
        $this->file      = request($file);
        $this->name      = $this->file['name'];
        $this->size      = $this->file['size'];
        $this->type      = $this->file['type'];
        $this->tmp       = $this->file['tmp_name'];
        $this->error     = $this->file['error'];
        $this->extension = $this->parseExtension();
    }

    /**
     *    Get the file extension.
     *    @return string
     */
    public function parseExtension(): ?string
    {
        $ext = null;

        if (is_array($this->name)) {
            foreach ($this->name as $value) {
                $ext_values = explode('.', $value);
                $ext_values = end($ext_values);
                $ext[]      = '.' . $ext_values;
            }
        } else {
            $ext = explode('.', $this->name);
            $ext = '.' . end($ext);
        }

        return $ext;
    }

    /**
     *    Generate random string.
     *
     *    @param integer $length
     *    @return string
     */
    public function randomString(int $length = 10)
    {
        $char       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char       = str_shuffle($char);
        $charLength = strlen($char);
        $random     = null;

        for ($i = 0; $i < $length; $i++) {
            $random .= $char[rand(0, $charLength - 1)];
        }

        return $random;
    }
    /**
     * @return void
     * @param mixed $fileError
     */
    private function checkFilesErrors($fileError): void
    {
        if ($fileError > 0) {
            $this->uploadErrors[] = $this->filesErrorMessages[$fileError];
        }
    }
    /**
     * @return void
     * @param mixed $errorMessage
     */
    private function error($errorMessage): void
    {
        $this->uploadErrors[] = $errorMessage;
    }

    /**
     *    Move uploaded files.
     * @return void
     */
    public function move(): void
    {
        if (! is_dir(BASE_PATH . "/public/storage/" . $this->path)) {
            mkdir(BASE_PATH . "/public/storage/" . $this->path);
        }

        if (is_array($this->name)) {
            for ($file = 0; $file < count($this->name); $file++) {
                $tmp  = $this->tmp[$file];
                $name = $this->name[$file];

                if ($this->randomFileName == true) {
                    $this->newFileName = date('Ymd') . '-' . time() . '-' . $this->randomString();
                    $name              = $this->newFileName . $this->extension[$file];
                } elseif (! is_null($this->newFileName)) {
                    $name = $this->newFileName . $this->extension[$file];
                }

                $this->checkFilesErrors($this->error[$file]);

                if (move_uploaded_file($tmp, $this->path . $name)) {
                } else {
                    $this->error("Something went wrong. this file '{$this->name[$file]}' has not been uploaded");
                }
            }
        } else {
            $name = $this->name;

            if ($this->randomFileName == true) {
                $this->newFileName = date('Ymd') . '-' . time() . '-' . $this->randomString();
                $name              = $this->newFileName . $this->extension;
            } elseif (! is_null($this->newFileName)) {
                $name = $this->newFileName . $this->extension;
            }

            $this->checkFilesErrors($this->error);

            move_uploaded_file($this->tmp, BASE_PATH . "/public/storage/" . $this->path . $name);
        }
    }

    /**
     *    Get the file name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *    Get the file size.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     *    Get the file type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *    Get the tmp name.
     */
    public function getTmp()
    {
        return $this->tmp;
    }

    /**
     *    Get the file error.
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *    Get the file extension.
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     *    Check if there is no errors.
     * @return bool
     */
    public function success()
    {
        if (empty($this->uploadErrors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *    Display the upload errors.
     */
    public function displayUploadErrors()
    {
        return $this->uploadErrors;
    }
}