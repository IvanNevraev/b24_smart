<?php
namespace ChelBit\Smart\Core;

use Bitrix\Crm\Service\Container;
use Bitrix\Main\Diag\Debug;
use ChelBit\Smart\Core\Base\Smart;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Installer
{
    private array $classes = [];
    private function loadClassesFromDirectory($directory) : void
    {

        if (!is_dir($directory)) {
            throw new \Exception("Directory does not exist: $directory");
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $filePath = $file->getRealPath();
            $this->classes[] = $this->getDeclaredClasses($filePath);

        }
    }
    private function getDeclaredClasses($filePath) : string
    {
        // Читаем содержимое файла
        $contents = file_get_contents($filePath);
        $tokens = token_get_all($contents);
        $count = count($tokens);
        $namespace = "";
        for ($i = 0; $i < $count; $i++) {
            if($tokens[$i][0] === T_NAMESPACE){
                $namespace .= $tokens[$i+2][1]."\\";
            }
            if ($tokens[$i][0] === T_CLASS) {
                // Пропускаем анонимные классы
                if ($tokens[$i-1][0] === T_NEW) {
                    continue;
                }
                // Получаем имя класса
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j] === '{') {
                        return $namespace.$tokens[$i+2][1];
                    }
                }
            }
        }

        throw new \Exception("Class not found: $filePath");
    }

    /**
     * Производит "установку" смарт процесса. А именно:
     * 1. Проверяет наличие смарт процесса по имени, если нет создает
     * 2. Если смарт процесс есть, обновляет настройки смарт процесса
     * //ToDo
     * 3. Создает или обновляет связи
     * 4. Создает или обновляет поля
     * 5. Генерирует демонстрационные элементы
     * @param string $class
     * @return void
     */
    private function installSmart(string $class) : void
    {
        /**
         * @var Smart $class
         */
        try {
            $class::getFactory();
            echo "UPDATE FOR $class  ";
        }catch (\Throwable $e){
            echo "ADD FOR $class  ";
        }
    }
    public function run() : void
    {
        $this->loadClassesFromDirectory(Config::getSrmClassesDir());
        foreach ($this->getClasses() as $class){
            if(in_array($class, Config::getDefaultCrmClasses())){
                continue;
            }
            $this->installSmart($class);
        }
    }
    public function getClasses() : array
    {
        return $this->classes;
    }

}