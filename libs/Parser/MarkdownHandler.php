<?php namespace Todaymade\Daux\Parser;

interface MarkdownHandler
{

  public function __construct( $params );
  public function convertToHtml( $params );
  public function renderHtml( $params );

}
