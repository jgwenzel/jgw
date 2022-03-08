<?php namespace JGW;

/**
 * Node Class
 * Author: John Wenzel johngwenzel@gmail.com
 * Part of Huffman Code Package
 * See README.md for more Details
 * 
 * 02/03/2022
 * LICENSING
 * As long as you leave credit to the author, John Wenzel and his email untouched, 
 * you are free to use for non-commercial purposes. Commercial purposes, please contact me.
 * 
 * Node class is quite simple.
 * A Node is simply an object with some properties.
 * The $left and $right properties may be other
 * nodes.
 * 
 * Visual:
 * Node::name  - The string that identifies the node. "A", "B", "XN_T" etc.
 * Node::value - The frequency of the letter in the message (or) the sum of it's children.
 * Node::bin   - The binary Huffman code
 * Node::left  - Another Node or null
 * Node::right - Another Node or null
 * 
 */
class Node
{
    private $name;
    private $value;
    private $left = null;
    private $right = null;
    private $bin = '';

    function __construct( string $name, ?int $value, ?Node $left, ?Node $right, string $bin = 'O' ) {
        
        $this->setName($name);
        $this->setValue($value);
        $this->setLeft($left);
        $this->setRight($right);
        $this->setBin($bin);
    }

    private function setName( $name ): void {
        $this->name = $name;
    }

    /* Getter */
    public function name(): string {
        return $this->name;
    }

    /* Setter */
    public function setValue( int $value ): void {
        $this->value = $value;
    }

    /* Getter */
    public function value() {
        return $this->value;
    }

    /* Setter */
    public function setLeft( ?Node $left) {
        $this->left = $left;
    }

    /* Getter */
    public function left() {
        return $this->left;
    }

    /* Setter */
    public function setRight( ?Node $right) {
        $this->right = $right;
    }

    /* Getter */
    public function right() {
        return $this->right;
    }
    
   /* Setter */
    public function setBin( ?string $bin ): void {
        $this->bin = $bin;
    }

    /* Getter */
    public function bin(): string {
        return $this->bin;
    }
}
