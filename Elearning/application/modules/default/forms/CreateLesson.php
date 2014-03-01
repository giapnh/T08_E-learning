<?php
class Default_Form_CreateLesson extends Zend_Form {
	public function init() {
		$this->setAction ( '' )->setMethod ( 'post' );
		$title = $this->createElement ( "text", "title", array (
				"class" => "les_input",
		        "id" => "les_title",
		        "maxlength" => "255",
				"required" => true
		) );
		$tag = $this->createElement ( "text", "tag", array (
				"class" => "les_input",
                "id" => "les_tag",
		) );

// 		$les_dec = $this->createElement ( "textarea", "les_decreption", array (
//              "class" => "les_texarea",
//                 "id" => "les_decreption",
// 		) );
		
		
		$submit = $this->createElement ( "submit", "submit", array (
				"class" => "showlogfromm",
				"label" => "作成"
		) );
		$file_title = $this->createElement ( "text", "file_title[]", array (
				"name" => "file_title[]",
				"class" => "les_input file_title",
				"maxlength" => "255",
				"required" => true
		) );
// 		$file_dec = new Zend_Form_Element_Textarea("file_decreption");
		$this->setDecorators ( array (
				array (
						'viewScript',
						array (
								'viewScript' => 'create-lesson.phtml'
						)
				)
		) );
		$file_title->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		//$file_dec->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		//$file_upload->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$title->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$tag->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		//$les_dec->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$submit->removeDecorator ( 'DtDdWrapper' );

		$this->addElements ( array (
				$title,
				$tag,
// 				$les_dec,
// 				$file_dec,
				$file_title,
				//$file_upload,
				$submit
		) );
	}
}