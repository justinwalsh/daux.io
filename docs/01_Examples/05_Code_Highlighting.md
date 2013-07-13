Highlight.js highlights syntax in code examples on blogs, forums and in fact on any web pages. It's very easy to use because it works automatically: finds blocks of code, detects a language, highlights it. [Learn more.](http://softwaremaniacs.org/soft/highlight/en/)

**Python**

	@requires_authorization
	def somefunc(param1='', param2=0):
	    r'''A docstring'''
	    if param1 > param2: # interesting
	        print 'Gre\'ater'
	    return (param2 - param1 + 1) or None

	class SomeClass:<br>    pass

	>>> message = '''interpreter
	... prompt'''


**Python's profiler output**

		   261917242 function calls in 686.251 CPU seconds

	       ncalls  tottime  filename:lineno(function)
	       152824  513.894  {method 'sort' of 'list' objects}
	    129590630   83.894  rrule.py:842(__cmp__)
	    129590630   82.439  {cmp}
	       153900    1.296  rrule.py:399(_iter)
	304393/151570    0.963  rrule.py:102(_iter_cached)


**Ruby**

	class A < B; def self.create(object = User) object end end
	class Zebra; def inspect; "X#{2 + self.object_id}" end end

	module ABC::DEF
	  include Comparable

	  # @param test
	  # @return [String] nothing
	  def foo(test)
	    Thread.new do |blockvar|
	      ABC::DEF.reverse(:a_symbol, :'a symbol', :<=>, 'test' + test)
	    end.join
	  end

	  def [](index) self[index] end
	  def ==(other) other == self end
	end

	anIdentifier = an_identifier
	Constant = 1
	render action: :new

**Haml**

	!!! XML
	%html
	  %body
	    %h1.jumbo{:id=>"a", :style=>'font-weight: normal', :title=>title} highlight.js
	    /html comment
	    -# ignore this line
	    %ul(style='margin: 0')
	    -items.each do |i|
	      %i= i
	    = variable
	    =variable2
	    ~ variable3
	    ~variable4
	    The current year is #{DataTime.now.year}.

**Perl**

	# loads object
	sub load
	{
	  my $flds = $c->db_load($id,@_) || do {
	    Carp::carp "Can`t load (class: $c, id: $id): '$!'"; return undef
	  };
	  my $o = $c->_perl_new();
	  $id12 = $id / 24 / 3600;
	  $o->{'ID'} = $id12 + 123;
	  #$o->{'SHCUT'} = $flds->{'SHCUT'};
	  my $p = $o->props;
	  my $vt;
	  $string =~ m/^sought_text$/;
	  $items = split //, 'abc';
	  for my $key (keys %$p)
	  {
	    if(${$vt.'::property'}) {
	      $o->{$key . '_real'} = $flds->{$key};
	      tie $o->{$key}, 'CMSBuilder::Property', $o, $key;
	    }
	  }
	  $o->save if delete $o->{'_save_after_load'};
	  return $o;
	}

	=head1 NAME
	POD till the end of file

**PHP**

	require_once 'Zend/Uri/Http.php';

	abstract class URI extends BaseURI
	{

	  /**
	   * Returns a URI
	   *
	   * @return  URI
	   */
	  static public function _factory($stats = array(), $uri = 'http')
	  {
	      $uri = explode(':', $uri, 0b10);
	      $schemeSpecific = isset($uri[1]) ? $uri[1] : '';
	      $desc = 'Multi
	line description';

	      // Security check
	      if (!ctype_alnum($scheme)) {
	          throw new Zend_Uri_Exception('Illegal scheme');
	      }

	      return [
	        'uri' => $uri,
	        'value' => null,
	      ];
	  }
	}

	__halt_compiler () ; datahere
	datahere
	datahere */
	datahere

**Scala**

	object abstractTypes extends Application {
	  abstract class SeqBuffer {
	    type T; val element: Seq[T]; def length = element.length
	  }
	}

	/** Turn command line arguments to uppercase */
	object Main {
	  def main(args: Array[String]) {
	    val res = for (a <- args) yield a.toUpperCase
	    println("Arguments: " + res.toString)
	  }
	}

	/** Maps are easy to use in Scala. */
	object Maps {
	  val colors = Map("red" -> 0xFF0000,
	                   "turquoise" -> 0x00FFFF,
	                   "black" -> 0x000000,
	                   "orange" -> 0xFF8040,
	                   "brown" -> 0x804000)
	  def main(args: Array[String]) {
	    for (name <- args) println(
	      colors.get(name) match {
	        case Some(code) =>
	          name + " has code: " + code
	        case None =>
	          "Unknown color: " + name
	      }
	    )
	  }
	}

**Go**

	package main

	import (
	    "fmt"
	    "rand"
	    "os"
	)

	const (
	    Sunday = iota
	    Partyday
	    numberOfDays  // this constant is not exported
	)

	type Foo interface {
	    FooFunc(int, float32) (complex128, []int)
	}

	// simple comment
	type Bar struct {
	    os.File /* multi
	    line
	    comment */

	    PublicData chan int
	}

	func main() {
	    ch := make(chan int)
	    ch <- 1
	    x, ok := <- ch
	    ok = true
	    x = nil
	    float_var := 1.0e10
	    defer fmt.Println('\'')
	    defer fmt.Println(`exitting now\`)
	    var fv1 float64 = 0.75
	    go println(len("hello world!"))
	    return
	}

**XML**

	<?xml version="1.0"?>
	<response value="ok" xml:lang="en">
	  <text>Ok</text>
	  <comment html_allowed="true"/>
	  <ns1:description><![CDATA[
	  CDATA is <not> magical.
	  ]]></ns1:description>
	  <a></a> <a/>
	</response>


**HTML (with inline css and javascript)**

	<!DOCTYPE html>
	<title>Title</title>

	<style>body {width: 500px;}</style>

	<script type="application/javascript">
	  function $init() {return true;}
	</script>

	<body>
	  <p checked class="title" id='title'>Title</p>
	  <!-- here goes the rest of the page -->
	</body>

**Lasso**

	<?LassoScript
	/* Lasso 8 */
	  local('query' = 'SELECT * FROM `'+var:'table'+'` WHERE `id` > 10
	    ORDER BY `Name` LIMIT 30');
	  Inline: -Username=$DBuser, -Password=$DBpass, -Database=$DBname, -sql=#query;
	    var("class.name" = (found_count != 0 ? `subtotal` | `nonefound`));
	    records;
	      output: ?><tr>[loop_count]</tr><?=;
	    /records;
	  /Inline;
	?><div class="[$class.name]">[found_count]</div>
	[noprocess] causes [delimiters] to be skipped until the next [/noprocess]
	<?lasso
	/* Lasso 9 */
	  define strings_combine(value::string, ...other)::string => {
	    local(result = #value->append(#other->asString))
	    return #result
	  }
	  /**! descriptive text */
	  define person => type {
	    data name::string, protected nickname
	    data birthdate::date
	    data private ssn = null
	    public showName() => return .'name'
	    protected fullName() => '"' + .nickname + '"' + .'name'
	    public ssnListed => .ssn ? true | false
	  }
	  define person->name=(value) => {
	    .'name' = #value
	    return self->'name'
	  }
	  // query expression
	  with n in array(-1, 0xABCD, 3.14159e14)
	  let swapped = pair(#n->second, #n->first)
	  group #swapped by #n->first into t
	  let key = #t->key
	  order by #key
	  select pair(#key, #t)
	  do {^
	    #n->upperCase
	  ^}
	?>

**Markdown**

	# hello world

	you can write text [with links](http://example.com).

	* one _thing_ has *em*phasis
	* two __things__ are **bold**

	---

	hello world
	===========

	<this_is inline="xml"></this_is>

	> markdown is so cool

	    so are code segments

	1. one thing (yeah!)
	2. two thing `i can write code`, and `more` wipee!

**AsciiDoc**

	Hello, World!
	============
	Author Name, <author@domain.foo>

	you can write text http://example.com[with links], optionally
	using an explicit link:http://example.com[link prefix].

	* single quotes around a phrase place 'emphasis'
	** alternatively, you can put underlines around a phrase to add _emphasis_
	* astericks around a phrase make the text *bold*
	* pluses around a phrase make it +monospaced+

	- escape characters are supported
	- you can escape a quote inside emphasized text like 'here\'s johnny!'

	term:: definition
	 another term:: another definition

	// this is just a comment

	Let's make a break.

	'''

	////
	we'll be right with you

	after this brief interruption.
	////

	== We're back!

	Want to see a image::images/tiger.png[Tiger]?

	.Nested highlighting
	++++
	<this_is inline="xml"></this_is>
	++++

	____
	asciidoc is so powerful.
	____

	another quote:

	[quote, Sir Arthur Conan Doyle, The Adventures of Sherlock Holmes]
	____
	When you have eliminated all which is impossible, then whatever remains, however improbable, must be the truth.
	____

	Getting Literal
	---------------

	 want to get literal? prefix a line with a space.

	....
	I'll join that party, too.
	....

	. one thing (yeah!)
	. two thing `i can write code`, and `more` wipee!

	NOTE: AsciiDoc is quite cool, you should try it.

**Django templates**

	{% if articles|length %}
	{% for article in articles %}

	{# Striped table #}
	<tr class="{% cycle odd,even %}">
	  <td>{{ article|default:"Hi... "|escape }}</td>
	  <td {% if article.today %}class="today"{% endif %}>{{ article.date|date:"d.m.Y" }}</td>
	</tr>

	{% endfor %}
	{% endif %}

	{% comment %}
	Comments may be long and
	multiline.
	{% endcomment %}

**Handlebars**

	<h3>Hours</h3>

	<ul>
	  {{#each content.users}}
	  <li {{bindAttr hello="world"}}>{{firstName}}</li>
	  {{/each}}
	</ul>

**CSS**

	@media screen and (-webkit-min-device-pixel-ratio: 0) {
	  body:first-of-type pre::after {
	    content: 'highlight: ' attr(class);
	  }
	  body {
	    background: linear-gradient(45deg, blue, red);
	  }
	}

	@import url('print.css');
	@page:right {
	 margin: 1cm 2cm 1.3cm 4cm;
	}

	@font-face {
	  font-family: Chunkfive; src: url('Chunkfive.otf');
	}

	div.text,
	#content,
	li[lang=ru] {
	  font: Tahoma, Chunkfive, sans-serif;
	  background: url('hatch.png') /* wtf? */;  color: #F0F0F0 !important;
	  width: 100%;
	}

**SCSS**

	@import "compass/reset";

	// variables
	$colorGreen: #008000;
	$colorGreenDark: darken($colorGreen, 10);

	@mixin container {
	    max-width: 980px;
	}

	// mixins with parameters
	@mixin button($color:green) {
	    @if ($color == green) {
	        background-color: #008000;
	    }
	    @else if ($color == red) {
	        background-color: #B22222;
	    }
	}

	button {
	    @include button(red);
	}

	div,
	.navbar,
	#header,
	input[type="input"] {
	    font-family: "Helvetica Neue", Arial, sans-serif;
	    width: auto;
	    margin: 0 auto;
	    display: block;
	}

	.row-12 > [class*="spans"] {
	    border-left: 1px solid #B5C583;
	}

	// nested definitions
	ul {
	    width: 100%;
	    padding: {
	        left: 5px; right: 5px;
	    }
	  li {
	      float: left; margin-right: 10px;
	      .home {
	          background: url('http://placehold.it/20') scroll no-repeat 0 0;
	    }
	  }
	}

	.banner {
	    @extend .container;
	}

	a {
	  color: $colorGreen;
	  &:hover { color: $colorGreenDark; }
	  &:visited { color: #c458cb; }
	}

	@for $i from 1 through 5 {
	    .span#{$i} {
	        width: 20px*$i;
	    }
	}

	@mixin mobile {
	  @media screen and (max-width : 600px) {
	    @content;
	  }
	}

**JSON**

	[
	  {
	    "title": "apples",
	    "count": [12000, 20000],
	    "description": {"text": "...", "sensitive": false}
	  },
	  {
	    "title": "oranges",
	    "count": [17500, null],
	    "description": {"text": "...", "sensitive": false}
	  }
	]

**JavaScript**

	function $initHighlight(block, flags) {
	  try {
	    if (block.className.search(/\bno\-highlight\b/) != -1)
	      return processBlock(block, true, 0x0F) + ' class=""';
	  } catch (e) {
	    /* handle exception */

	    var e4x =
	        <div>Example
	            <p>1234</p></div>;
	  }
	  for (var i = 0 / 2; i < classes.length; i++) { // "0 / 2" should not be parsed as regexp
	    if (checkCondition(classes[i]) === undefined)
	      return /\d+[\s/]/g;
	  }
	}

**CoffeeScript**

	# Divisions
	x = 6/foo/i
	x = 6 /foo
	x = 6 / foo
	x = 6 /foo * 2/gm
	x = f /foo
	x = f / foo / gm
	x = f /foo * 2/6

	# Regexps
	x = f /6 * 2/ - 3
	x = f /foo * 2/gm
	x = if true then /\n/ else /[.,]+/

	grade = (student, period=(if b? then 7 else 6), messages={"A": "Excellent"}) ->
	  if student.excellentWork
	    "A+"
	  else if student.okayStuff
	    if student.triedHard then "B" else "B-"
	  else
	    "C"

	square = (x) -> x * x

	two = -> 2

	math =
	  root:   Math.sqrt
	  square: square
	  cube:   (x) -> x * square x

	race = (winner, runners...) ->
	  print winner, runners

	class Animal extends Being
	  constructor: (@name) ->

	  move: (meters) ->
	    alert @name + " moved #{meters}m."

	hi = `function() {
	  return [document.title, "Hello JavaScript"].join(": ");
	}`

	heredoc = """
	CoffeeScript subst test #{ 010 + 0xf / 0b10 + "nested string #{ /\n/ }"}
	"""

	###
	CoffeeScript Compiler v1.2.0
	Released under the MIT License
	###

	OPERATOR = /// ^ (
	?: [-=]>             # function
	) ///

**ActionScript**

	package org.example.dummy {
	    import org.dummy.*;

	    /*define package inline interface*/
	    public interface IFooBarzable {
	        public function foo(... pairs):Array;
	    }

	    public class FooBar implements IFooBarzable {
	        static private var cnt:uint = 0;
	        private var bar:String;

	        //constructor
	        public function TestBar(bar:String):void {
	            bar = bar;
	            ++cnt;
	        }

	        public function foo(... pairs):Array {
	            pairs.push(bar);
	            return pairs;
	        }
	    }
	}

**VBScript**

	' creating configuration storage and initializing with default values
	Set cfg = CreateObject("Scripting.Dictionary")

	' reading ini file
	for i = 0 to ubound(ini_strings)
	    s = trim(ini_strings(i))

	    ' skipping empty strings and comments
	    if mid(s, 1, 1) <> "#" and len(s) > 0 then
	      ' obtaining key and value
	      parts = split(s, "=", -1, 1)

	      if ubound(parts)+1 = 2 then
	        parts(0) = trim(parts(0))
	        parts(1) = trim(parts(1))

	        ' reading configuration and filenames
	        select case lcase(parts(0))
	          case "uncompressed""_postfix" cfg.item("uncompressed""_postfix") = parts(1)
	          case "f"
	                    options = split(parts(1), "|", -1, 1)
	                    if ubound(options)+1 = 2 then
	                      ' 0: filename,  1: options
	                      ff.add trim(options(0)), trim(options(1))
	                    end if
	        end select
	      end if
	    end if
	next

**VB.NET**

	Import System
	Import System.IO
	#Const DEBUG = True

	Namespace Highlighter.Test
	  ''' <summary>This is an example class.</summary>
	  Public Class Program
	    Protected Shared hello As Integer = 3
	    Private Const ABC As Boolean = False

	#Region "Code"
	    ' Cheers!
	    <STAThread()> _
	    Public Shared Sub Main(ByVal args() As String, ParamArray arr As Object) Handles Form1.Click
	      On Error Resume Next
	      If ABC Then
	        While ABC : Console.WriteLine() : End While
	        For i As Long = 0 To 1000 Step 123
	          Try
	            System.Windows.Forms.MessageBox.Show(CInt("1").ToString())
	          Catch ex As Exception       ' What are you doing? Well...
	            Dim exp = CType(ex, IOException)
	            REM ORZ
	            Return
	          End Try
	        Next
	      Else
	        Dim l As New System.Collections.List<String>()
	        SyncLock l
	          If TypeOf l Is Decimal And l IsNot Nothing Then
	            RemoveHandler button1.Paint, delegate
	          End If
	          Dim d = New System.Threading.Thread(AddressOf ThreadProc)
	          Dim a = New Action(Sub(x, y) x + y)
	          Static u = From x As String In l Select x.Substring(2, 4) Where x.Length > 0
	        End SyncLock
	        Do : Laugh() : Loop Until hello = 4
	      End If
	    End Sub
	#End Region
	  End Class
	End Namespace


**HTTP**

	POST /task?id=1 HTTP/1.1
	Host: example.org
	Content-Type: application/json; charset=utf-8
	Content-Length: 19

	{"status": "ok", "extended": true}

**Lua**

	--[[
	Simple signal/slot implementation
	]]
	local signal_mt = {
	    __index = {
	        register = table.insert
	    }
	}
	function signal_mt.__index:emit(... --[[ Comment in params ]])
	    for _, slot in ipairs(self) do
	        slot(self, ...)
	    end
	end
	local function create_signal()
	    return setmetatable({}, signal_mt)
	end

	-- Signal test
	local signal = create_signal()
	signal:register(function(signal, ...)
	    print(...)
	end)
	signal:emit('Answer to Life, the Universe, and Everything:', 42)

	--[==[ [=[ [[
	Nested ]]
	multi-line ]=]
	comment ]==]
	[==[ Nested
	[=[ multi-line
	[[ string
	]] ]=] ]==]

**AppleScript**

	repeat 5 times
	    if foo is greater than bar then
	        display dialog "Hello there"
	    else
	        beep
	    end if
	end repeat

	(* comment (*nested comment*) *)
	on do_something(s, y)
	    return {s + pi, y mod 4}
	end do_something

	do shell script "/bin/echo 'hello'"


**Delphi**

	TList=Class(TObject)
	Private
	  Some: String;
	Public
	  Procedure Inside; // Suxx
	End;{TList}

	Procedure CopyFile(InFileName,var OutFileName:String);
	Const
	  BufSize=4096; (* Huh? *)
	Var
	  InFile,OutFile:TStream;
	  Buffer:Array[1..BufSize] Of Byte;
	  ReadBufSize:Integer;
	Begin
	  InFile:=Nil;
	  OutFile:=Nil;
	  Try
	    InFile:=TFileStream.Create(InFileName,fmOpenRead);
	    OutFile:=TFileStream.Create(OutFileName,fmCreate);
	    Repeat
	      ReadBufSize:=InFile.Read(Buffer,BufSize);
	      OutFile.Write(Buffer,ReadBufSize);
	    Until ReadBufSize<>BufSize;
	    Log('File '''+InFileName+''' copied'#13#10);
	  Finally
	    InFile.Free;
	    OutFile.Free;
	  End;{Try}
	End;{CopyFile}

**Java**

	/**
	 * @author John Smith <john.smith@example.com>
	 * @version 1.0
	*/
	package l2f.gameserver.model;

	import java.util.ArrayList;

	public abstract class L2Character extends L2Object {
	  public static final Short ABNORMAL_EFFECT_BLEEDING = 0x0001; // not sure

	  public void moveTo(int x, int y, int z) {
	    _ai = null;
	    _log.warning("Should not be called");
	    if (1 > 5) {
	      return;
	    }
	  }

	  /** Task of AI notification */
	  @SuppressWarnings( { "nls", "unqualified-field-access", "boxing" })
	  public class NotifyAITask implements Runnable {
	    private final CtrlEvent _evt;

	    public void run() {
	      try {
	        getAI().notifyEvent(_evt, null, null);
	      } catch (Throwable t) {
	        t.printStackTrace();
	      }
	    }
	  }
	}

**C++**

	#include <iostream>

	int main(int argc, char *argv[]) {

	  /* An annoying "Hello World" example */
	  for (auto i = 0; i < 0xFFFF; i++)
	    cout << "Hello, World!" << endl;

	  char c = '\n';
	  unordered_map <string, vector<string> > m;
	  m["key"] = "\\\\"; // this is an error

	  return -2e3 + 12l;
	}

**Objective C**

	#import <UIKit/UIKit.h>
	#import "Dependency.h"

	@protocol WorldDataSource
	@optional
	- (NSString*)worldName;
	@required
	- (BOOL)allowsToLive;
	@end

	@interface Test : NSObject <HelloDelegate, WorldDataSource> {
	  NSString *_greeting;
	}

	@property (nonatomic, readonly) NSString *greeting;
	- (IBAction) show;
	@end

	@implementation Test

	@synthesize test=_test;

	+ (id) test {
	  return [self testWithGreeting:@"Hello, world!\nFoo bar!"];
	}

	+ (id) testWithGreeting:(NSString*)greeting {
	  return [[[self alloc] initWithGreeting:greeting] autorelease];
	}

	- (id) initWithGreeting:(NSString*)greeting {
	  if ( (self = [super init]) ) {
	    _greeting = [greeting retain];
	  }
	  return self;
	}

	- (void) dealloc {
	  [_greeting release];
	  [super dealloc];
	}

	@end

**Vala**

	using DBus;

	namespace Test {
	  class Foo : Object {
	    public signal void some_event ();   // definition of the signal
	    public void method () {
	      some_event ();                    // emitting the signal (callbacks get invoked)
	    }
	  }
	}

	/* defining a class */
	class Track : GLib.Object, Test.Foo {              /* subclassing 'GLib.Object' */
	  public double mass;                  /* a public field */
	  public double name { get; set; }     /* a public property */
	  private bool terminated = false;     /* a private field */
	  public void terminate() {            /* a public method */
	    terminated = true;
	  }
	}

	const ALL_UPPER_CASE = "you should follow this convention";

	var t = new Track();      // same as: Track t = new Track();
	var s = "hello";          // same as: string s = "hello";
	var l = new List<int>();       // same as: List<int> l = new List<int>();
	var i = 10;               // same as: int i = 10;


	#if (ololo)
	Regex regex = /foo/;
	#endif

	/*
	 * Entry point can be outside class
	 */
	void main () {
	  var long_string = """
	    Example of "verbatim string".
	    Same as in @"string" in C#
	  """
	  var foo = new Foo ();
	  foo.some_event.connect (callback_a);      // connecting the callback functions
	  foo.some_event.connect (callback_b);
	  foo.method ();
	}

**C**

	using System;

	#pragma warning disable 414, 3021

	public class Program
	{
	    /// <summary>The entry point to the program.</summary>
	    public static int Main(string[] args)
	    {
	        Console.WriteLine("Hello, World!");
	        string s = @"This
	""string""
	spans
	multiple
	lines!";
	        return 0;
	    }
	}

	async Task<int> AccessTheWebAsync()
	{
	    // ...
	    string urlContents = await getStringTask;
	    return urlContents.Length;
	}

**F**

	open System

	// Single line comment...
	(*
	  This is a
	  multiline comment.
	*)
	let checkList alist =
	    match alist with
	    | [] -> 0
	    | [a] -> 1
	    | [a; b] -> 2
	    | [a; b; c] -> 3
	    | _ -> failwith "List is too big!"


	type IEncoding =
	    abstract Encode : string -> string
	    abstract Decode : string -> string

	let text = "Some text..."
	let text2 = @"A ""verbatim"" string..."
	let catalog = """
	Some "long" string...
	"""

	let rec fib x = if x <= 2 then 1 else fib(x-1) + fib(x-2)

	let fibs =
	    Async.Parallel [ for i in 0..40 -> async { return fib(i) } ]
	    |> Async.RunSynchronously

	type Sprocket(gears) =
	  member this.Gears : int = gears

	[<AbstractClass>]
	type Animal =
	  abstract Speak : unit -> unit

	type Widget =
	  | RedWidget
	  | GreenWidget

	type Point = {X: float; Y: float;}

	[<Measure>]
	type s
	let minutte = 60<s>

**D**

	#!/usr/bin/rdmd
	// Computes average line length for standard input.
	import std.stdio;

	/+
	  this is a /+ nesting +/ comment
	+/

	enum COMPILED_ON = __TIMESTAMP__;  // special token

	enum character = '©';
	enum copy_valid = '&copy;';
	enum backslash_escaped = '\\';

	// string literals
	enum str = `hello "world"!`;
	enum multiline = r"lorem
	ipsum
	dolor";  // wysiwyg string, no escapes here allowed
	enum multiline2 = "sit
	amet
	\"adipiscing\"
	elit.";
	enum hex = x"66 6f 6f";   // same as "foo"

	#line 5

	// float literals
	enum f = [3.14f, .1, 1., 1e100, 0xc0de.01p+100];

	static if (something == true) {
	   import std.algorithm;
	}

	void main() pure nothrow @safe {
	    ulong lines = 0;
	    double sumLength = 0;
	    foreach (line; stdin.byLine()) {
	        ++lines;
	        sumLength += line.length;
	    }
	    writeln("Average line length: ",
	        lines ? sumLength / lines : 0);
	}

**RenderMan RSL**

	#define TEST_DEFINE 3.14
	/*  plastic surface shader
	 *
	 *  Pixie is:
	 *  (c) Copyright 1999-2003 Okan Arikan. All rights reserved.
	 */

	surface plastic (float Ka = 1, Kd = 0.5, Ks = 0.5, roughness = 0.1;
	                 color specularcolor = 1;) {
	  normal Nf = faceforward (normalize(N),I);
	  Ci = Cs * (Ka*ambient() + Kd*diffuse(Nf)) + specularcolor * Ks *
	       specular(Nf,-normalize(I),roughness);
	  Oi = Os;
	  Ci *= Oi;
	}

**RenderMan RIB**

	FrameBegin 0
	Display "Scene" "framebuffer" "rgb"
	Option "searchpath" "shader" "+&:/home/kew"
	Option "trace" "int maxdepth" [4]
	Attribute "visibility" "trace" [1]
	Attribute "irradiance" "maxerror" [0.1]
	Attribute "visibility" "transmission" "opaque"
	Format 640 480 1.0
	ShadingRate 2
	PixelFilter "catmull-rom" 1 1
	PixelSamples 4 4
	Projection "perspective" "fov" 49.5502811377
	Scale 1 1 -1

	WorldBegin

	ReadArchive "Lamp.002_Light/instance.rib"
	Surface "plastic"
	ReadArchive "Cube.004_Mesh/instance.rib"
	# ReadArchive "Sphere.010_Mesh/instance.rib"
	# ReadArchive "Sphere.009_Mesh/instance.rib"
	ReadArchive "Sphere.006_Mesh/instance.rib"

	WorldEnd
	FrameEnd

**MEL (Maya Embedded Language)**

	proc string[] getSelectedLights()

	{
	  string $selectedLights[];

	  string $select[] = `ls -sl -dag -leaf`;

	  for ( $shape in $select )
	  {
	    // Determine if this is a light.
	    //
	    string $class[] = getClassification( `nodeType $shape` );


	    if ( ( `size $class` ) > 0 && ( "light" == $class[0] ) )
	    {
	      $selectedLights[ `size $selectedLights` ] = $shape;
	    }
	  }

	  // Result is an array of all lights included in

	  // current selection list.
	  return $selectedLights;
	}

**GLSL**

	// vertex shader
	#version 150
	in  vec2 in_Position;
	in  vec3 in_Color;

	out vec3 ex_Color;
	void main(void) {
	    gl_Position = vec4(in_Position.x, in_Position.y, 0.0, 1.0);
	    ex_Color = in_Color;
	}


	// geometry shader
	#version 150

	layout(triangles) in;
	layout(triangle_strip, max_vertices = 3) out;

	void main() {
	  for(int i = 0; i < gl_in.length(); i++) {
	    gl_Position = gl_in[i].gl_Position;
	    EmitVertex();
	  }
	  EndPrimitive();
	}


	// fragment shader
	#version 150
	precision highp float;

	in  vec3 ex_Color;
	out vec4 gl_FragColor;

	void main(void) {
	    gl_FragColor = vec4(ex_Color, 1.0);
	}

**SQL**

	BEGIN;
	CREATE TABLE "topic" (
	    "id" serial NOT NULL PRIMARY KEY,
	    "forum_id" integer NOT NULL,
	    "subject" varchar(255) NOT NULL
	);
	ALTER TABLE "topic" ADD CONSTRAINT forum_id FOREIGN KEY ("forum_id") REFERENCES "forum" ("id");

	-- Initials
	insert into "topic" ("forum_id", "subject") values (2, 'D''artagnian');

	select count(*) from cicero_forum;

	-- this line lacks ; at the end to allow people to be sloppy and omit it in one-liners
	COMMIT

**SmallTalk**

	Object>>method: num
	    "comment 123"
	    | var1 var2 |
	    (1 to: num) do: [:i | |var| ^i].
	    Klass with: var1.
	    Klass new.
	    arr := #('123' 123.345 #hello Transcript var $@).
	    arr := #().
	    var2 = arr at: 3.
	    ^ self abc

	heapExample
	    "HeapTest new heapExample"
	    "Multiline
	    decription"
	    | n rnd array time sorted |
	    n := 5000.
	    "# of elements to sort"
	    rnd := Random new.
	    array := (1 to: n)
	                collect: [:i | rnd next].
	    "First, the heap version"
	    time := Time
	                millisecondsToRun: [sorted := Heap withAll: array.
	    1
	        to: n
	        do: [:i |
	            sorted removeFirst.
	            sorted add: rnd next]].
	    Transcript cr; show: 'Time for Heap: ' , time printString , ' msecs'.
	    "The quicksort version"
	    time := Time
	                millisecondsToRun: [sorted := SortedCollection withAll: array.
	    1
	        to: n
	        do: [:i |
	            sorted removeFirst.
	            sorted add: rnd next]].
	    Transcript cr; show: 'Time for SortedCollection: ' , time printString , ' msecs'

**Lisp**

	#!/usr/bin/env csi

	(defun prompt-for-cd ()
	   "Prompts
	    for CD"
	   (prompt-read "Title" 1.53 1 2/4 1.7 1.7e0 2.9E-4 +42 -7 #b001 #b001/100 #o777 #O777 #xabc55 #c(0 -5.6))
	   (prompt-read "Artist" &rest)
	   (or (parse-integer (prompt-read "Rating") :junk-allowed t) 0)
	  (if x (format t "yes") (format t "no" nil) ;and here comment
	  )
	  ;; second line comment
	  '(+ 1 2)
	  (defvar *lines*)                ; list of all lines
	  (position-if-not #'sys::whitespacep line :start beg))
	  (quote (privet 1 2 3))
	  '(hello world)
	  (* 5 7)
	  (1 2 34 5)
	  (:use "aaaa")
	  (let ((x 10) (y 20))
	    (print (+ x y))
	  )

**Clojure**

	;   You must not remove this notice, or any other, from this software.

	(ns ^{:doc "The core Clojure language."
	       :author "Rich Hickey"}
	  clojure.core)

	(def unquote)

	(def
	  ^{:macro true
	    :added "1.0"}
	  let (fn* let [&form &env & decl] (cons 'let* decl)))

	(def

	 defn (fn defn [&form &env name & fdecl]
	        (let [m (conj {:arglists (list 'quote (sigs fdecl))} m)
	              m (let [inline (:inline m)
	                      ifn (first inline)
	                      iname (second inline)]
	                  ;; same as: (if (and (= 'fn ifn) (not (symbol? iname))) ...)
	                  (if (if (clojure.lang.Util/equiv 'fn ifn)
	                        (if (instance? clojure.lang.Symbol iname) false true))
	                    ;; inserts the same fn name to the inline fn if it does not have one
	                    (assoc m :inline (cons ifn (cons (clojure.lang.Symbol/intern (.concat (.getName ^clojure.lang.Symbol name) "__inliner"))
	                                                     (next inline))))
	                    m))
	              m (conj (if (meta name) (meta name) {}) m)]
	          (list 'def (with-meta name m)
	                ;;todo - restore propagation of fn name
	                ;;must figure out how to convey primitive hints to self calls first
	                (cons `fn fdecl) ))))

	(. (var defn) (setMacro))

**Ini file**

	;Settings relating to the location and loading of the database
	[Database]
	ProfileDir=.
	ShowProfileMgr=smart
	Profile1_Name[] = "\|/_-=MegaDestoyer=-_\|/"
	DefaultProfile=True
	AutoCreate = no

	[AutoExec]
	use-prompt="prompt"
	Glob=autoexec_*.ini
	AskAboutIgnoredPlugins=0

**Apache**

	# rewrite`s rules for wordpress pretty url
	LoadModule rewrite_module  modules/mod_rewrite.so
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule . index.php [NC,L]

	ExpiresActive On
	ExpiresByType application/x-javascript  "access plus 1 days"

	<Location /maps/>
	  RewriteMap map txt:map.txt
	  RewriteMap lower int:tolower
	  RewriteCond %{REQUEST_URI} ^/([^/.]+)\.html$ [NC]
	  RewriteCond ${map:${lower:%1}|NOT_FOUND} !NOT_FOUND
	  RewriteRule .? /index.php?q=${map:${lower:%1}} [NC,L]
	</Location>

**nginx**

	user  www www;
	worker_processes  2;
	pid /var/run/nginx.pid;
	error_log  /var/log/nginx.error_log  debug | info | notice | warn | error | crit;

	events {
	    connections   2000;
	    use kqueue | rtsig | epoll | /dev/poll | select | poll;
	}

	http {
	    log_format main      '$remote_addr - $remote_user [$time_local] '
	                         '"$request" $status $bytes_sent '
	                         '"$http_referer" "$http_user_agent" '
	                         '"$gzip_ratio"';

	    send_timeout 3m;
	    client_header_buffer_size 1k;

	    gzip on;
	    gzip_min_length 1100;

	    #lingering_time 30;

	    server {
	        server_name   one.example.com  www.one.example.com;
	        access_log   /var/log/nginx.access_log  main;

	        rewrite (.*) /index.php?page=$1 break;

	        location / {
	            proxy_pass         http://127.0.0.1/;
	            proxy_redirect     off;
	            proxy_set_header   Host             $host;
	            proxy_set_header   X-Real-IP        $remote_addr;
	            charset            koi8-r;
	        }

	        location /api/ {
	            fastcgi_pass 127.0.0.1:9000;
	        }

	        location ~* \.(jpg|jpeg|gif)$ {
	            root         /spool/www;
	        }
	    }
	}

**Diff**

	Index: languages/ini.js
	===================================================================
	--- languages/ini.js    (revision 199)
	+++ languages/ini.js    (revision 200)
	@@ -1,8 +1,7 @@
	 hljs.LANGUAGES.ini =
	 {
	   case_insensitive: true,
	-  defaultMode:
	-  {
	+  defaultMode: {
	     contains: ['comment', 'title', 'setting'],
	     illegal: '[^\\s]'
	   },

	*** /path/to/original timestamp
	--- /path/to/new      timestamp
	***************
	*** 1,3 ****
	--- 1,9 ----
	+ This is an important
	+ notice! It should
	+ therefore be located at
	+ the beginning of this
	+ document!

	! compress the size of the
	! changes.

	  It is important to spell

**DOS batch files**

	cd \
	copy a b
	ping 192.168.0.1
	@rem ping 192.168.0.1
	net stop sharedaccess
	del %tmp% /f /s /q
	del %temp% /f /s /q
	ipconfig /flushdns
	taskkill /F /IM JAVA.EXE /T

	cd Photoshop/Adobe Photoshop CS3/AMT/
	if exist application.sif (
	    ren application.sif _application.sif
	) else (
	    ren _application.sif application.sif
	)

	taskkill /F /IM proquota.exe /T

	sfc /SCANNOW

	set path = test

	xcopy %1\*.* %2

**Bash**

	#!/bin/bash

	###### BEGIN CONFIG
	ACCEPTED_HOSTS="/root/.hag_accepted.conf"
	BE_VERBOSE=false
	###### END CONFIG

	if [ "$UID" -ne 0 ]
	then
	 echo "Superuser rights is required"
	 echo 'Printing the # sign'
	 exit 2
	fi

	if test $# -eq 0
	then
	elif test [ $1 == 'start' ]
	else
	fi

	genApacheConf(){
	 if [[ "$2" = "www" ]]
	 then
	  full_domain=$1
	 else
	  full_domain=$2.$1
	 fi
	 host_root="${APACHE_HOME_DIR}$1/$2/$(title)"
	 echo -e "# Host $1/$2 :"
	}

**CMake**

	project(test)
	cmake_minimum_required(VERSION 2.6)

	# IF LINUX
	if (${CMAKE_SYSTEM_NAME} MATCHES Linux)
	    message("\nOS:\t\tLinux")
	endif()

	# IF WINDOWS
	if (${CMAKE_SYSTEM_NAME} MATCHES Windows)
	    message("\nOS:\t\tWindows")
	endif()

	set(test test0.cpp test1.cpp test2.cpp)

	include_directories(./)

	set(EXECUTABLE_OUTPUT_PATH ../bin)

	add_subdirectory(src)

	add_executable(test WIN32 ${test})

	target_link_libraries(test msimg32)

**Axapta**

	class ExchRateLoadBatch extends RunBaseBatch {
	  ExchRateLoad rbc;
	  container currencies;
	  boolean actual;
	  boolean overwrite;
	  date beg;
	  date end;

	  #define.CurrentVersion(5)

	  #localmacro.CurrentList
	    currencies,
	    actual,
	    beg,
	    end
	  #endmacro
	}

	public boolean unpack(container packedClass) {
	  container       base;
	  boolean         ret;
	  Integer         version    = runbase::getVersion(packedClass);

	  switch (version) {
	    case #CurrentVersion:
	      [version, #CurrentList] = packedClass;
	      return true;
	    default:
	      return false;
	  }
	  return ret;
	}

**Oracle Rules Language**

	//This is a comment
	ABORT "You experienced an abort.";
	WARN "THIS IS A WARNING";
	CALL "RIDER_X";
	DONE;
	FOR EACH X IN CSV_FILE "d:\lodestar\user\d377.lse"
	 LEAVE FOR;
	END FOR;
	IF ((BILL_KW = 0) AND (KW > 0)) THEN
	END IF;
	INCLUDE "R1";
	LEAVE RIDER;
	SELECT BILL_PERIOD
	   WHEN "WINTER"
	      BLOCK KWH
	      FROM 0 TO 400 CHARGE $0.03709
	      FROM 400 CHARGE $0.03000
	      TOTAL $ENERGY_CHARGE_WIN;
	   WHEN "SUMMER"
	      $VOLTAGE_DISCOUNT_SUM = $0.00
	   OTHERWISE
	      $VOLTAGE_DISCOUNT_SUM = $1.00
	END SELECT;
	/* Report top five peaks */
	LABEL PK.NM "Peak Number";
	SAVE_UPDATE MV TO TABLE "METERVALUE";

	FOR EACH INX IN ARRAYUPPERBOUND(#MYARRAY[])
	  #MYARRAY[INX].VALUE = 2;
	  CLEAR #MYARRAY[];
	END FOR

	//Interval Data
	HNDL_1_ADD_EDI = INTDADDATTRIBUTE(HNDL_1, "EDI_TRANSACTION", EDI_ID);
	HNDL_1_ADD_VAL_MSG = INTDADDVMSG(HNDL_1,"Missing (Status Code 9) values found");
	EMPTY_HNDL = INTDCREATEHANDLE('05/03/2006 00:00:00', '05/03/2006 23:59:59', 3600, "Y", "0", " ");

**1С

	#Если Клиент Тогда
	Перем СимвольныйКодКаталога = "ля-ля-ля"; //комментарий
	Функция Сообщить(Знач ТекстСообщения, ТекстСообщения2) Экспорт //комментарий к функции
	  x=ТекстСообщения+ТекстСообщения2+"
	  |строка1
	  |строка2
	  |строка3";
	КонецФункции
	#КонецЕсли

	// Процедура ПриНачалеРаботыСистемы
	//
	Процедура ПриНачалеРаботыСистемы()
	  Обработки.Помощник.ПолучитьФорму("Форма").Открыть();
	  d = '21.01.2008'
	КонецПроцедуры

**AVR Assembler**

	;* Title:       Block Copy Routines
	;* Version:     1.1

	.include "8515def.inc"

	    rjmp    RESET   ;reset handle

	.def    flashsize=r16       ;size of block to be copied

	flash2ram:
	    lpm         ;get constant
	    st  Y+,r0       ;store in SRAM and increment Y-pointer
	    adiw    ZL,1        ;increment Z-pointer
	    dec flashsize
	    brne    flash2ram   ;if not end of table, loop more
	    ret

	.def    ramtemp =r1     ;temporary storage register
	.def    ramsize =r16        ;size of block to be copied

**VHDL**

	 /*
	 * RS-trigger with assynch. reset
	 */

	library ieee;
	use ieee.std_logic_1164.all;

	entity RS_trigger is
	    generic (T: Time := 0ns);
	    port ( R, S  : in  std_logic;
	           Q, nQ : out std_logic;
	           reset, clock : in  std_logic );
	end RS_trigger;

	architecture behaviour of RS_trigger is
	    signal QT: std_logic; -- Q(t)
	begin
	    process(clock, reset) is
	        subtype RS is std_logic_vector (1 downto 0);
	    begin
	        if reset = '0' then
	            QT <= '0';
	        else
	            if rising_edge(C) then
	                if not (R'stable(T) and S'stable(T)) then
	                    QT <= 'X';
	                else
	                    case RS'(R&S) is
	                        when "01" => QT <= '1';
	                        when "10" => QT <= '0';
	                        when "11" => QT <= 'X';
	                        when others => null;
	                    end case;
	                end if;
	            end if;
	        end if;
	    end process;

	    Q  <= QT;
	    nQ <= not QT;
	end architecture behaviour;

**Parser 3**

	@CLASS
	base

	@USE
	module.p

	@BASE
	class

	# Comment for code
	@create[aParam1;aParam2][local1;local2]
	  ^connect[mysql://host/database?ClientCharset=windows-1251]
	  ^for[i](1;10){
	    <p class="paragraph">^eval($i+10)</p>
	    ^connect[mysql://host/database]{
	      $tab[^table::sql{select * from `table` where a='1'}]
	      $var_Name[some${value}]
	    }
	  }

	  ^rem{
	    Multiline comment with code: $var
	    ^while(true){
	      ^for[i](1;10){
	        ^sleep[]
	      }
	    }
	  }
	  ^taint[^#0A]

	@GET_base[]
	## Comment for code
	  # Isn't comment
	  $result[$.hash_item1[one] $.hash_item2[two]]

**TeX**

	\documentclass{article}
	\usepackage[koi8-r]{inputenc}
	\hoffset=0pt
	\voffset=.3em
	\tolerance=400
	\newcommand{\eTiX}{\TeX}
	\begin{document}
	\section*{Highlight.js}
	\begin{table}[c|c]
	$\frac 12\, + \, \frac 1{x^3}\text{Hello \! world}$ & \textbf{Goodbye\~ world} \\\eTiX $ \pi=400 $
	\end{table}
	Ch\'erie, \c{c}a ne me pla\^\i t pas! % comment \b
	G\"otterd\"ammerung~45\%=34.
	$$
	    \int\limits_{0}^{\pi}\frac{4}{x-7}=3
	$$
	\end{document}

**Haskell**

	{-# LANGUAGE TypeSynonymInstances #-}
	module Network.UDP
	( DataPacket(..)
	, openBoundUDPPort
	, openListeningUDPPort
	, pingUDPPort
	, sendUDPPacketTo
	, recvUDPPacket
	, recvUDPPacketFrom
	) where

	{- this is a {- nested -} comment -}

	import qualified Data.ByteString as Strict (ByteString, concat, singleton)
	import qualified Data.ByteString.Lazy as Lazy (ByteString, toChunks, fromChunks)
	import Data.ByteString.Char8 (pack, unpack)
	import Network.Socket hiding (sendTo, recv, recvFrom)
	import Network.Socket.ByteString (sendTo, recv, recvFrom)

	-- Type class for converting StringLike types to and from strict ByteStrings
	class DataPacket a where
	  toStrictBS :: a -> Strict.ByteString
	  fromStrictBS :: Strict.ByteString -> a

	instance DataPacket Strict.ByteString where
	  toStrictBS = id
	  {-# INLINE toStrictBS #-}
	  fromStrictBS = id
	  {-# INLINE fromStrictBS #-}

	openBoundUDPPort :: String -> Int -> IO Socket
	openBoundUDPPort uri port = do
	  s <- getUDPSocket
	  bindAddr <- inet_addr uri
	  let a = SockAddrInet (toEnum port) bindAddr
	  bindSocket s a
	  return s

	pingUDPPort :: Socket -> SockAddr -> IO ()
	pingUDPPort s a = sendTo s (Strict.singleton 0) a >> return ()

**Erlang**

	-module(ssh_cli).

	-behaviour(ssh_channel).

	-include("ssh.hrl").
	%% backwards compatibility
	-export([listen/1, listen/2, listen/3, listen/4, stop/1]).

	%% state
	-record(state, {
	    cm,
	    channel
	   }).

	test(Foo)->Foo.

	init([Shell, Exec]) ->
	    {ok, #state{shell = Shell, exec = Exec}};
	init([Shell]) ->
	    false = not true,
	    io:format("Hello, \"~p!~n", [atom_to_list('World')]),
	    {ok, #state{shell = Shell}}.

	concat([Single]) -> Single;
	concat(RList) ->
	    EpsilonFree = lists:filter(
	        fun (Element) ->
	            case Element of
	                epsilon -> false;
	                _ -> true
	            end
	        end,
	        RList),
	    case EpsilonFree of
	        [Single] -> Single;
	        Other -> {concat, Other}
	    end.

	union_dot_union({union, _}=U1, {union, _}=U2) ->
	    union(lists:flatten(
	        lists:map(
	            fun (X1) ->
	                lists:map(
	                    fun (X2) ->
	                        concat([X1, X2])
	                    end,
	                    union_to_list(U2)
	                )
	            end,
	            union_to_list(U1)
	        ))).


**Erlang REPL**

	1> Str = "abcd".
	"abcd"
	2> L = test:length(Str).
	4
	3> Descriptor = {L, list_to_atom(Str)}.
	{4,abcd}
	4> L.
	4
	5> b().
	Descriptor = {4,abcd}
	L = 4
	Str = "abcd"
	ok
	6> f(L).
	ok
	7> b().
	Descriptor = {4,abcd}
	Str = "abcd"
	ok
	8> {L, _} = Descriptor.
	{4,abcd}
	9> L.
	4
	10> 2#101.
	5
	11> 1.85e+3.
	1850

**Rust**

	use std;

	import std::io;
	export fac, test1;

	123;                               // type int
	123u;                              // type uint
	123_u;                             // type uint
	0xff00;                            // type int
	0xff_u8;                           // type u8
	0b1111_1111_1001_0000_i32;         // type i32
	123.0;                             // type float
	0.1;                               // type float
	3f;                                // type float
	0.1f32;                            // type f32
	12E+99_f64;                        // type f64

	/* Factorial */
	fn fac(n: int) -> int {
	    let s: str = "This is
	a multi-line string.

	It ends with an unescaped '\"'.";
	    let c: char = 'Ф';

	    let result = 1, i = 1;
	    while i <= n { // No parens around the condition
	        result *= i;
	        i += 1;
	    }
	    ret result;
	}

	pure fn pure_length<T>(ls: list<T>) -> uint { /* ... */ }

	type t = map::hashtbl<int,str>;
	let x = id::<int>(10);

	// Define some modules.
	#[path = "foo.rs"]
	mod foo;

	iface seq<T> {
	    fn len() -> uint;
	}

	impl <T> of seq<T> for [T] {
	    fn len() -> uint { vec::len(self) }
	    fn iter(b: fn(T)) {
	        for elt in self { b(elt); }
	    }
	}

	enum list<T> {
	    nil;
	    cons(T, @list<T>);
	}

	let a: list<int> = cons(7, @cons(13, @nil));

**Matlab**

	n = 20; % number of points
	points = [random('unid', 100, n, 1), random('unid', 100, n, 1)];
	len = zeros(1, n - 1);
	points = sortrows(points);
	%% Initial set of points
	plot(points(:,1),points(:,2));
	for i = 1: n-1
	    len(i) = points(i + 1, 1) - points(i, 1);
	end
	while(max(len) > 2 * min(len))
	    [d, i] = max(len);
	    k = on_margin(points, i, d, -1);
	    m = on_margin(points, i + 1, d, 1);
	    xm = 0; ym = 0;
	%% New point
	    if(i == 1 || i + 1 == n)
	        xm = mean(points([i,i+1],1))
	        ym = mean(points([i,i+1],2))
	    else
	        [xm, ym] = dlg1(points([k, i, i + 1, m], 1), ...
	            points([k, i, i + 1, m], 2))
	    end

	    points = [ points(1:i, :); [xm, ym]; points(i + 1:end, :)];
	end

	function [net] = get_fit_network(inputs, targets)
	    % Create Network
	    numHiddenNeurons = 20;  % Adjust as desired
	    net = newfit(inputs,targets,numHiddenNeurons);
	    net.trainParam.goal = 0.01;
	    net.trainParam.epochs = 1000;
	    % Train and Apply Network
	    [net,tr] = train(net,inputs,targets);
	end

	foo_matrix = [1, 2, 3; 4, 5, 6]''';
	foo_cell = {1, 2, 3; 4, 5, 6}''.'.';

**R**

	library(ggplot2)

	centre <- function(x, type, ...) {
	  switch(type,
	         mean = mean(x),
	         median = median(x),
	         trimmed = mean(x, trim = .1))
	}

	myVar1
	myVar.2
	data$x
	foo "bar" baz
	# test "test"
	"test # test"

	(123) (1) (10) (0.1) (.2) (1e-7)
	(1.2e+7) (2e) (3e+10) (0x0) (0xa)
	(0xabcdef1234567890) (123L) (1L)
	(0x10L) (10000000L) (1e6L) (1.1L)
	(1e-3L) (4123.381E-10i)
	(3.) (3.E10) # BUG: .E10 should be part of number

	# Numbers in some different contexts
	1L
	0x40
	.234
	3.
	1L + 30
	plot(cars, xlim=20)
	plot(cars, xlim=0x20)
	foo<-30
	my.data.3 <- read() # not a number
	c(1,2,3)
	1%%2

	"this is a quote that spans
	multiple lines
	\"

	is this still a quote? it should be.
	# even still!

	" # now we're done.

	'same for
	single quotes #'

	# keywords
	NULL, NA, TRUE, FALSE, Inf, NaN, NA_integer_,
	NA_real_, NA_character_, NA_complex_, function,
	while, repeat, for, if, in, else, next, break,
	..., ..1, ..2

	# not keywords
	the quick brown fox jumped over the lazy dogs
	null na true false inf nan na_integer_ na_real_
	na_character_ na_complex_ Function While Repeat
	For If In Else Next Break .. .... "NULL" `NULL` 'NULL'

	# operators
	+, -, *, /, %%, ^, >, >=, <, <=, ==, !=, !, &, |, ~,
	->, <-, <<-, $, :, ::

	# infix operator
	foo %union% bar
	%"test"%
	`"test"`

**Mizar**

	::: ## Lambda calculus

	environ

	  vocabularies LAMBDA,
	      NUMBERS,
	      NAT_1, XBOOLE_0, SUBSET_1, FINSEQ_1, XXREAL_0, CARD_1,
	      ARYTM_1, ARYTM_3, TARSKI, RELAT_1, ORDINAL4, FUNCOP_1;

	  :: etc...

	begin

	reserve D for DecoratedTree,
	        p,q,r for FinSequence of NAT,
	        x for set;

	definition
	  let D;

	  attr D is LambdaTerm-like means
	    (dom D qua Tree) is finite &
	::>                          *143,306
	    for r st r in dom D holds
	      r is FinSequence of {0,1} &
	      r^<*0*> in dom D implies D.r = 0;
	end;

	registration
	  cluster LambdaTerm-like for DecoratedTree of NAT;
	  existence;
	::>       *4
	end;

	definition
	  mode LambdaTerm is LambdaTerm-like DecoratedTree of NAT;
	end;

	::: Then we extend this ordinary one-step beta reduction, that is,
	:::  any subterm is also allowed to reduce.
	definition
	  let M,N;

	  pred M beta N means
	    ex p st
	      M|p beta_shallow N|p &
	      for q st not p is_a_prefix_of q holds
	        [r,x] in M iff [r,x] in N;
	end;

	theorem Th4:
	  ProperPrefixes (v^<*x*>) = ProperPrefixes v \/ {v}
	proof
	  thus ProperPrefixes (v^<*x*>) c= ProperPrefixes v \/ {v}
	  proof
	    let y;
	    assume y in ProperPrefixes (v^<*x*>);
	    then consider v1 such that
	A1: y = v1 and
	A2: v1 is_a_proper_prefix_of v^<*x*> by TREES_1:def 2;
	 v1 is_a_prefix_of v & v1 <> v or v1 = v by A2,TREES_1:9;
	then
	 v1 is_a_proper_prefix_of v or v1 in {v} by TARSKI:def 1,XBOOLE_0:def 8;
	then  y in ProperPrefixes v or y in {v} by A1,TREES_1:def 2;
	    hence thesis by XBOOLE_0:def 3;
	  end;
	  let y;
	  assume y in ProperPrefixes v \/ {v};
	then A3: y in ProperPrefixes v or y in {v} by XBOOLE_0:def 3;
	A4: now
	    assume y in ProperPrefixes v;
	    then consider v1 such that
	A5: y = v1 and
	A6: v1 is_a_proper_prefix_of v by TREES_1:def 2;
	 v is_a_prefix_of v^<*x*> by TREES_1:1;
	then  v1 is_a_proper_prefix_of v^<*x*> by A6,XBOOLE_1:58;
	    hence thesis by A5,TREES_1:def 2;
	  end;
	 v^{} = v by FINSEQ_1:34;
	  then
	 v is_a_prefix_of v^<*x*> & v <> v^<*x*> by FINSEQ_1:33,TREES_1:1;
	then  v is_a_proper_prefix_of v^<*x*> by XBOOLE_0:def 8;
	then  y in ProperPrefixes v or y = v & v in ProperPrefixes (v^<*x*>)
	  by A3,TARSKI:def 1,TREES_1:def 2;
	  hence thesis by A4;
	end;



**Special tests**

Explicit Python highlighting

	for x in [1, 2, 3]:
	  count(x)


Language set on <pre>

	for x in [1, 2, 3]:
	  count(x)

HTML5-style language class (language-python)
	
	for x in [1, 2, 3]:
	  count(x)

Replacing TAB with 4 spaces

	for x in [1, 2, 3]:
		count(x)

Custom markup

	<<a href="http://dev.w3.org/html5/spec/Overview.html#the-div-element">div</a> id="contents">
	  <del><p>Hello, World!</del><!-- A comment should not break merging --><ins>Goodbye, cruel world!</ins>
	</div>

Custom markup + TAB replacement

	for x in [1, 2, 3]:
	<span style="background:yellow">	</span>count(x)
		if x == 3:
		<span style="background:yellow">	</span>count(x + 1)

