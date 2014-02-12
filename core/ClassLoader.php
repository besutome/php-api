<?php

/**
* ClassLoader Class.
*
* @author Yosuke Ohshima
*/
class ClassLoader
{
    private $namespaces = array();

    public function __construct($namespaces = array())
    {
        $this->namespaces = $namespaces;
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function loadClass($class)
    {
        $class = ltrim($class, '\\');

        // 名前空間に属するクラスの場合
        if (false !== ($pos = strrpos($class, '\\'))) {
            $namespace = substr($class, 0, $pos);
            $class = substr($class, $pos + 1);

            // 登録されている名前空間に一致するものがあればファイルを読み込む
            foreach ($this->namespaces as $ns => $dir) {
                if (0 === strpos($namespace, $ns)) {
                    $path = $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

                    if (is_file($path)) {
                        require $path;

                        return true;
                    }
                } 
            }
        // 名前空間に属さないクラスで、ディレクトリが指定されている場合
        } elseif (isset($this->namespaces[''])) {
            $dir = $this->namespaces[''];
            $path = $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

            if (is_file($path)) {
                require $path;

                return true;
            }
        }
    }
}
