<?php namespace JGW;

require "Node.class.php";
require "BinaryTree.class.php";
 /**
  * Huffman Class
  * Author: John Wenzel johngwenzel@gmail.com
  * Dependencies: Node class and Binary Tree class by John Wenzel
  * 
  * See README.md for package details
  *
  * 02/03/2022
  * LICENSING
  * As long as you leave credit to the author, John Wenzel and his email untouched, 
  * you are free to use for non-commercial purposes. Commercial purposes, please contact me.
  * 
  * This is the _main_ class of the huffman package. All access happens
  * here.
  *
  * ERROR handling to be added as I discover what kind of errors may occur.
  * So far there haven't been any I didn't fix.
  *
  * MESSAGE CONSTRAINTS: The only allowable characters are [A-Za-z ] (the alphabet
  * and space.) All other characters will simply be discarded. These constraints are
  * governed by preg_replace functions in setMessage(). You can change those to your
  * liking. If you want numbers, just change to [0-9A-Za-z ]. All will be converted to
  * Uppercase.
  */
  
class Huffman
{
    /* SET CONFIG constants to your liking */

    /* CONFIG -*/
    const MAX_TREE_LOOPS = 10;  //set this to avoid runaway loops
    const SPACE = '_';          //a substitue for [:space:] is needed
    /* ENDCONFIG */

    private $message;           //message as string
    private $message_arr;       //message as array
    private $alphabet;          //alaphet as assoc array with frequency
    private $alphabet_str;      //alphabet as string
    private $code;              //message encoded
    private $codes;             //alphabet codes
    private $codes_str;         //alphabet codes string
    private $binary_tree_class; //accessor for class
    private $tree;              //The binary tree: type Node from Node.class.php

    /**
     * You may simply instantiate the class with a message, then call 
     * $instance->getCode().
     */
    function __construct( string $message = null ) {
        if(isset( $message )) {
            $result = $this->encode( $message );
        }
    }
    /**
     * encode() is the main function that calls all the other functions 
     * to encode the message.
     * @param: $message
     * @return: bool
     */
    public function encode( $message ): bool {
        $this->setMessage( $message );
        $this->setMessageArray();
        $this->makeAlphabet();
        $this->makeAlphabetString();
        if($this->makeTree() && is_object($this->binary_tree_class)) {
            $tree = $this->binary_tree_class->getTree();
            $this->tree = $this->binary_tree_class->setBinaryCodes( $tree );
            $this->binary_tree_class->setAlpha( $this->getAlphabet() );
            $this->setCodes($this->binary_tree_class->getAlpha());
            $this->messageToCode();
            return true;
        }
        return false; 
    }

    /**
     * Encodes the message with Huffman Codes that were generated.
     * @param: None. Uses $this->message_arr, the array of the message.
     */
    private function messageToCode(): void {
        $code = "";
        foreach( $this->message_arr as $char ) {
            $code .= $this->codes[$char];
        }
        $this->setCode($code);
    }

    /**
     * Mother function that calls others to build the Binary Tree.
     * Most of the work is done by the BinaryTree class.
     * @return: true
     */
    private function makeTree(): bool {
        $queue = $this->getQueue();
        $node_queue = $this->getNodeQueue( $queue ); 
        $this->binary_tree_class = new BinaryTree();
        $this->binary_tree_class->buildTree( $node_queue );
        return true;
    }

    /**
     * A simple utility that filters user message and discards
     * non-Alpha characters. It encodes spaces with SPACE constant
     * defined at the top of class.
     * @param: $message
     * @return: message filtered message
     * Customize. You may change filtering behavior by changing regex
     * in each preg_replace.
     */
    private function prepareMessage( $message ): string {
        $message = strtoupper(trim($message));
        $matches = "";
        $message = preg_replace( "/[^A-Z ]/", '', $message );
        $message = preg_replace( "/\s+/", self::SPACE, $message );
        return $message;
    }

    /*********** SETTERS & GETTERS ************
     * Mostlh obvious, so comments are minimal
     */

    /**
     * The queue is like the alaphabet array with frequencies
     * but has a different structure as you can see. This
     * is used to initialize the Node Queue.
     */
    private function getQueue() {
        $queue = array();
        $i=0;
        foreach( $this->alphabet as $key => $value ) {
            $queue[$i][0] = $key;
            $queue[$i++][1] = $value;
        }
        return $queue;
    }
    
    /**
     * The Node Queue is an array of "primitive" Nodes, meaning
     * they have no childern. The will be connected together when
     * the tree is built.
     * @param: array $queue (right above)
     * @return array of primitive nodes
     */
    private function getNodeQueue( array $queue ): array {
        $queue = $this->getQueue();
        $nodes = array();
        foreach($queue as $q) {
            //init all as Primitive Nodes with name, value, and no children or bin
            $nodes[] = new Node($q[0], $q[1], null, null, '');
        }
        return $nodes;
    }

    private function setCode( string $code ): void {
        $this->code = $code;
    }

    public function getCode(): string {
        return $this->code;
    }

    private function setCodes( array $codes ): void {
        $this->codes = $codes;
        $this->makeCodesString();
    }

    public function getCodes(): array {
        return $this->codes;
    }

    private function setMessage( string $message ): void {
        $this->message = $this->prepareMessage($message);
    }

    public function getMessage(): string {
        return $this->message;
    }

    private function setMessageArray(): void {
        $this->message_arr = str_split($this->message);
    }

    public function getMessageArray(): array {
        return $this->message_arr;
    }

    /**
     * Alphabet is sorted by frequency ascending.
     */
    private function makeAlphabet(): void {
        $alphabet = array_count_values( $this->message_arr );
        asort( $alphabet );
        $this->alphabet = $alphabet;
    }

    public function getAlphabet(): array {
        return $this->alphabet;
    }

    public function getAlphabetString(): string {
        return $this->alphabet_str;
    }

    /* for visual output */
    public function makeAlphabetString(): void {
        $alphabet_str = "";
        foreach( $this->alphabet as $key => $value ) {
            $alphabet_str .= $key . ":" . $value ." ";
        }
        $this->alphabet_str = trim($alphabet_str);
    }

    public function getCodesString(): string {
        return $this->codes_str;
    }

    /* for visual output */
    public function makeCodesString(): void {
        $codes_str = "";
        foreach( $this->codes as $key => $value ) {
            $codes_str .= $key . ":" . $value ." ";
        }
        $this->codes_str = trim($codes_str);
    }

    /*********** OUTPUT ************/
    public function printMessage($eol = "\n"): void {
        echo "MESSAGE: " . $this->message . $eol;
    }

    public function printAlphabet($eol = "\n"): void {
        echo "ALPHABET: " . $this->alphabet_str . $eol;
    }

    public function printCodes($eol  = "\n"): void {
        echo "CODES: " . $this->codes_str . $eol;
    }

    public function printCode($eol = "\n"): void {
        echo "HUFFMAN CODE: " . $this->code . $eol;
    }

    public function printTree( ?Node $tree, $loops = 0, $label = "ROOT") {

        if(is_object($tree) && $loops < self::MAX_TREE_LOOPS ) {
            $loops++;
            if(false !== strpos($label, "RIGHT")) {
                echo "              $label ---> " . $tree->name() . "\n";
            }
            else {
                echo $tree->name() . " <--- $label\n";
            }
            
            if(is_object($tree->left())) {
                $this->printTree( $tree->left(), $loops,  $tree->name() . "'s LEFT" );
            }
            if(is_object($tree->right())) {
                $this->printTree( $tree->right(), $loops, $tree->name() . "'s RIGHT" );
            }
        }
        return true;
    }

}
