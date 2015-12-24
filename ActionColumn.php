<?php
namespace carono\components;

use Yii;

class ActionColumn extends \yii\grid\ActionColumn
{
	public $text;
	public $checkUrlAccess = true;

	protected function renderDataCellContent($model, $key, $index)
	{
		return preg_replace_callback(
			'/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
			$name = $matches[1];
			if (isset($this->buttons[$name])) {
				$url = $this->createUrl($name, $model, $key, $index);
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

	public function createUrl($action, $model, $key, $index)
	{
		if (method_exists($model, 'getUrl') && ($url = $model->getUrl($action))) {
			return $url;
		} else {
			return parent::createUrl($action, $model, $key, $index);
		}
	}

	private static function registerModifyScript($id, $iconSend, $iconRevert, $iconOk = "glyphicon-ok",
		$iconError = 'glyphicon-remove', $iconWait = "glyphicon-time")
	{
		$iconSend = strpos($iconSend, 'fa-') !== false ? "fa " . $iconSend : "glyphicon " . $iconSend;
		$iconRevert = strpos($iconRevert, 'fa-') !== false ? "fa " . $iconRevert : "glyphicon " . $iconRevert;
		$iconOk = strpos($iconOk, 'fa-') !== false ? "fa " . $iconOk : "glyphicon " . $iconOk;
		$iconError = strpos($iconError, 'fa-') !== false ? "fa " . $iconError : "glyphicon " . $iconError;
		$iconWait = strpos($iconWait, 'fa-') !== false ? "fa " . $iconWait : "glyphicon " . $iconWait;
		$script
			= <<<EOT
    $('[ajax-link=$id]').on('click', function () {
    	var elem=$(this);
		$.ajax({
			type: "POST",
			url: $(this).attr('href'),
			dataType: 'json',
			async: true,
			success: function (data) {
				if (elem.attr('revert-url') == elem.attr('href')){
					elem.find('.glyphicon, .fa').attr('class','').addClass('$iconSend');
					elem.attr('href', elem.attr('send-url'));
				}else if(elem.attr('send-url') == elem.attr('href')){
					elem.find('.glyphicon, .fa').attr('class','').addClass('$iconRevert');
					elem.attr('href', elem.attr('revert-url'));
				}else{
					elem.find('.glyphicon, .fa').attr('class','').addClass('$iconOk').off('click');
					elem.on('click',function(){return false;})
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
				elem.find('.glyphicon, .fa').attr('class','').addClass('$iconWait');
			},
			error: function(jqXHR, textStatus, errorThrown){
				elem.find('.glyphicon, .fa').attr('class','').addClass('$iconError');
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