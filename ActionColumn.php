<?php
namespace carono\components;

use Doctrine\Common\Inflector\Inflector;
use Yii;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $text;
    public $checkUrlAccess = true;
    public $visibleButton;

    public function init()
    {
        parent::init();
        $methods = get_class_methods($this);
        preg_match_all('/button(\w+)/', join(" ", $methods), $m);
        foreach ($m[1] as $button) {
            $this->buttons[lcfirst($button)] = function ($url, $model, $key) use ($button) {
                $result = call_user_func_array([$this, "button" . $button], [$url, $model, $key]);
                if ($result instanceof ButtonColumn) {
                    return $result->asLink();
                } else {
                    return $result;
                }
            };
        }
    }

    public function buttonUpload($url, $model, $key)
    {
        $button = new ButtonColumn();
        $button->icon = "glyphicon glyphicon-upload";
        $button->title = "Загрузить";
        $button->url = $url;
        return $button;
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback(
            '/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $action = $matches[1];
            $name = Inflector::camelize(strtr($matches[1], ['/' => '_', '\\' => '_']));
            if (isset($this->buttons[$name]) && $this->buttonIsVisible($name, $model, $key, $index)) {
                $url = $this->createUrl($action, $model, $key, $index);
                if (!$this->checkUrlAccess || RoleManager::checkAccessByUrl($url)) {
                    return call_user_func($this->buttons[$name], $url, $model, $key);
                } else {
                    return '';
                }
            } else {
                return '';
            }
        }, $this->template
        );
    }

    public function buttonIsVisible($name, $model, $key, $index)
    {
        if ($this->visibleButton) {
            return call_user_func($this->visibleButton, $name, $model, $key, $index);
        } else {
            return true;
        }
    }

    public function createUrl($action, $model, $key, $index)
    {
        if (method_exists($model, 'getUrl') && ($url = $model->getUrl($action)) && !$this->urlCreator) {
            return $url;
        } else {
            return parent::createUrl($action, $model, $key, $index);
        }
    }

    protected static function registerModifyScript($id, $send, $revert, $ok = "glyphicon-ok",
        $error = 'glyphicon-remove', $wait = "glyphicon-time")
    {
        $send = strpos($send, 'fa-') !== false ? "fa " . $send : "glyphicon " . $send;
        $revert = strpos($revert, 'fa-') !== false ? "fa " . $revert : "glyphicon " . $revert;
        $ok = strpos($ok, 'fa-') !== false ? "fa " . $ok : "glyphicon " . $ok;
        $error = strpos($error, 'fa-') !== false ? "fa " . $error : "glyphicon " . $error;
        $wait = strpos($wait, 'fa-') !== false ? "fa " . $wait : "glyphicon " . $wait;
        $script
            = <<<EOT
    $('[ajax-link=$id]').on('click', function () {
    	var elem=$(this);
    	if (!elem.attr('revert-url')){
    		elem.attr('revert-url',elem.attr('href'));
    	}
		$.ajax({
			type: "POST",
			url: $(this).attr('href'),
			dataType: 'json',
			async: true,
			success: function (data) {
				if (elem.attr('revert')){
					elem.removeAttr('revert');
				}else{
					elem.attr('revert', 1);
				}
				if (elem.attr('revert')){
					elem.find('.glyphicon, .fa').attr('class','').addClass('$revert');
					elem.attr('href', elem.attr('revert-url'));
				}else{
					elem.find('.glyphicon, .fa').attr('class','').addClass('$send');
					elem.attr('href', elem.attr('send-url'));
				}
				if (data['message']) {
					$(elem).parent().tooltip({
						title: data['message'],
						placement: 'right',
						trigger:'hover',
						delay: {show: 500, hide: 500}
					}).tooltip('show').on('hidden.bs.tooltip',function(){ $(this).tooltip('destroy') });
				}
			},
			beforeSend: function (){
				elem.find('.glyphicon, .fa').attr('class','').addClass('$wait');
			},
			error: function(jqXHR, textStatus, errorThrown){
				elem.find('.glyphicon, .fa').attr('class','').addClass('$error');
				if (jqXHR.responseJSON['message']) {
					$(elem).parent().tooltip({
						title:jqXHR.responseJSON['message'],
						placement : 'right',
						trigger:'hover',
						delay: {show: 500, hide: 500}
					}).tooltip('show').on('hidden.bs.tooltip',function(){ $(this).tooltip('destroy') });
				}
			}
		});
        return false;
    })
EOT;
        Yii::$app->getView()->registerJs($script);
    }
}