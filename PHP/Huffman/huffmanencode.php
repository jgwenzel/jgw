<?php namespace JGW;

require "Huffman.class.php";

/**
 * Huffman Code (helper page)
 * Change message in Huffman() below to try it out.
 * 
 * 02/03/2022
 * LICENSING
 * As long as you leave credit to the author, John Wenzel and his email untouched, 
 * you are free to use for non-commercial purposes. Commercial purposes, please contact me.
 * 
 * Quick Tip:
 * 
 * Run on cli like:
 * 
 * php -f Huffman.class.php
 * 
 * Or include in a PHP web app.
 * 
 * Author: John Wenzel johngwenzel@gmail.com
 * Date: 3/2/2022
 * Files Included:
 *   Huffman.class.php
 *   BinaryTree.class.php 
 *   Node.class.php
 *   huffmanencode.php (this file) 
 * 
 * See README.md for much more info.
 *
 * This example message is from the Wikipedia page about Huffman Codes.
 * The code generated all checks out! (; 
 * See Here: https://en.wikipedia.org/wiki/Huffman_coding. */

 //$huff = new Huffman("A DEAD DAD CEDED A BAD BABE A BEADED ABACA BED");

$huff = new Huffman("MADAM IM ADAM");

$huff->printMessage();
$huff->printAlphabet();
$huff->printCodes();
$huff->printCode();
