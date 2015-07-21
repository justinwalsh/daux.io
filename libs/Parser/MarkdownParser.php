<?php namespace Todaymade\Daux\Parser;

use Todaymade\Daux\Parser;

/**
 * MarkdownParser
 */
class MarkdownParser
{

  private $_handler = null;

  public function __construct( $params ){
    if( empty($params) || !is_array($params) ){
      $params = array();
    }
    if( empty($params['handler']) ){
      $params['handler'] = 'parsedown';
    }
    $this->setHandler( $params['handler'] );
  }

  public function setHandler( $handler ){
    if( isset($handler) ){
      if( is_callable($handler) ){
        $this->_handler = $handler;
      }
      else if( is_string($handler) ){
        $handlerClass = 'Todaymade\Daux\Parser\MarkdownHandler' . ucfirst($handler);
        if( class_exists( $handlerClass ) ){
          $this->_handler = new $handlerClass( $params );
        }
        else {
          throw new Exception("Error Loading {$handlerClass}", 1);
        }
      }
    }
    if( empty($this->_handler) ){
      $this->_handler = new \Parsedown();
    }
  }

  public function getHandler(){
    return $this->_handler;
  }

  public function __call( $method, $params ){
    if( $this->getHandler() && method_exists($this->getHandler(), $method) ){
      return call_user_func_array(array($this->getHandler(), $method), $params);
    }
    return null;
  }

}