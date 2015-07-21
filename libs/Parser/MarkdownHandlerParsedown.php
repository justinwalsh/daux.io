<?php namespace Todaymade\Daux\Parser;

use Todaymade\Daux\Exception;

use Erusev\Parsedown;

class MarkdownHandlerParsedown implements MarkdownHandler {

  public function __construct( $params ){
    $handlerClass = '\Parsedown';
    if( !class_exists( $handlerClass ) ){
      throw new Exception("Error Loading {$handlerClass}", 1);
    }
    $this->_handler = new $handlerClass();
  }

  public function convertToHtml( $params ){
    if( is_string($params) ){
      return $this->_handler->text( $params );
    }
    return null;
  }

  public function renderHtml( $params ){
    echo $this->convertToHtml( $params );
  }

}