/**
 * Wordpress SyntaxHighlighter brush for Swift
 * By Randex, randexdev.com
 *
 * Copyright (C) 2014 Randexdev
 *
 * 
 * Licensed under a GNU Lesser General Public License.
 * http://creativecommons.org/licenses/LGPL/2.1/
 * 
 */

SyntaxHighlighter.brushes.Swift = function() {
	
	var datatypes =	'Character Bool Double Float Int Int8 Int16 Int32 Int64 UInt UInt8 UInt16 UInt32 UInt64 AnyObject String Void';
	
	var keywords = 'IBAction IBOutlet true false nil ';
	keywords += 'super self Self copy ';
	keywords += 'as break case class ';
	keywords += 'continue convenience dynamic final private objc ';
	keywords += 'default get set willSet didSet ';
	keywords += 'else enum if is in infix internal for fallthough func import inout ';
    keywords += 'let lazy mutable ';
	keywords += 'naked namespace new noinline noreturn nothrow NSCopying NSManaged mutating objc override operator optional prefix protocol ';
	keywords += 'private public required return ';
	keywords += 'static struct switch ';
	keywords += 'T typealias UnsafePointer var ';
	keywords += 'unowned weak where while';
    
    var funcs = 'advance enumerate find filter join min map max print println sizeof sort init'
    
    var operators = '= : ( ) , < > / * + - && || | & ~= >= <= == \( += -= { } '
    
	this.regexList = [
        { regex: SyntaxHighlighter.regexLib.singleLineCComments,	css: 'comments' },			// one line comments
        { regex: SyntaxHighlighter.regexLib.multiLineCComments,		css: 'comments' },			// multiline comments
        { regex: SyntaxHighlighter.regexLib.doubleQuotedString,		css: 'string' },			// strings
        { regex: SyntaxHighlighter.regexLib.singleQuotedString,		css: 'string' },			// strings
		{ regex: new RegExp(this.getKeywords(datatypes), 'gm'),     css: 'color1' },		// datatypes
        { regex: new RegExp('\\b[A-Z]+\\w+', 'g'),                  css: 'color2' },		// capitalized names
		{ regex: new RegExp(this.getKeywords(keywords), 'gm'),		css: 'keyword' },		// keyword
        { regex: new RegExp(this.getKeywords(funcs), 'gm'),         css: 'functions' },		// functions
		{ regex: new RegExp('@\\w+\\b', 'g'),						css: 'keyword' },		// keyword
		];
	
    var prefixes = ['NS', 'UI', 'SK', 'CG', 'CI', 'AV', 'CK', 'CF', 'CF', 'EK', 'GK', 'MK', 'CA'];
    for (var i = 0; i < prefixes.length; i++) {
        this.regexList.push({regex: new RegExp('\\b' + prefixes[i]  + '\\w+\\b', 'g'), css:'color1'})
    }
}

SyntaxHighlighter.brushes.Swift.prototype = new SyntaxHighlighter.Highlighter();
SyntaxHighlighter.brushes.Swift.aliases = ['swift'];
