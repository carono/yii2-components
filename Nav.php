<?php
namespace carono\components;

class Nav extends \yii\bootstrap\Nav
{

    public function init()
    {
        $this->items = $this->filterItems($this->items);
        parent::init();
    }

    /**
     * @param $items
     *
     * @return array
     */
    public function filterItems($items)
    {
        $newItems = [];
        foreach ($items as $item) {
            if ($this->checkItemAccess($item)) {
                if (isset($item["items"])) {
                    $item["items"] = $this->filterItems($item["items"]);
                    if (!$item["items"]) {
                        continue;
                    }
                }
                $newItems[] = $item;
            }
        }
        return $newItems;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    protected function checkItemAccess($item)
    {
        if (isset($item["url"])) {
            if ($item["url"] != "#" && (!isset($item["checkAccess"])) || !empty($item["checkAccess"])) {
                if (!RoleManager::checkAccessByUrl($item["url"])) {
                    return false;
                }
            }
        }
        return true;
    }
}