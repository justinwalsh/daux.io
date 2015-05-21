<?php namespace Todaymade\Daux\Parser;

use Todaymade\Daux\Exception;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\CommonMarkConverter;

class MarkdownHandlerCommonmark implements MarkdownHandler {

  public function __construct( $params ){
    $handlerClass = 'League\CommonMark\CommonMarkConverter';
    if( !class_exists( $handlerClass ) ){
      throw new Exception("Error Loading {$handlerClass}", 1);
    }
    $this->_handler = new $handlerClass();
  }

  public function convertToHtml( $params ){
    if( is_string($params) ){
      return $this->_handler->convertToHtml( $params );
    }
    return null;
  }

  public function renderHtml( $params ){
    echo $this->convertToHtml( $params );
  }

}