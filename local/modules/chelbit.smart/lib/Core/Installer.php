<?php
namespace ChelBit\Smart\Core;

use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\ORM\Data\AddResult;
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

    /**
     * @return AddResult
     * @throws \Exception
     */
    public static function addEntityType(string $smartClass) : AddResult
    {
        $data = [
            "NAME" => $smartClass::getEntityTypeName(),
            "TITLE" => $smartClass::getEntityTypeTitle(),
            "ENTITY_TYPE_ID" => TypeTable::getNextAvailableEntityTypeId(),
        ];
        return TypeTable::add(
            [
                "IS_CATEGORIES_ENABLED" => static::isCategoriesEnabled(),
                "IS_STAGES_ENABLED" => static::isStagesEnabled(),
                "IS_BEGIN_CLOSE_DATES_ENABLED" => static::isBeginCloseDatesEnabled(),
                "IS_CLIENT_ENABLED" => static::isClientEnabled(),
                "IS_LINK_WITH_PRODUCTS" => static::isLinkWithProductsEnabled(),
                "IS_MYCOMPANY_ENABLED"=> static::isMyCompanyEnabled(),
                "IS_DOCUMENTS_ENABLED"=> static::isDocumentsEnabled(),
                "IS_SOURCE_ENABLED"=> static::isSourceEnabled(),
                "IS_OBSERVERS_ENABLED" => static::isObserversEnabled(),
                "IS_RECYCLEBIN_ENABLED" => static::isRecyclebinEnabled(),
                "IS_AUTOMATION_ENABLED" => static::isAutomationEnabled(),
                "IS_BIZ_PROC_ENABLED" => static::isBizProcEnabled(),
                "IS_SET_OPEN_PERMISSIONS" => static::isSetOpenPermissions(),
            ]
        );
    }

}