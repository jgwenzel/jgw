<?php namespace JGW;

/**
 * BinaryTree Class
 * Author: John Wenzel johngwenzel@gmail.com
 * Part of Huffman Code Package
 * See README.md for more Details
 * 
 * 02/03/2022
 * LICENSING
 * As long as you leave credit to the author, John Wenzel and his email untouched, 
 * you are free to use for non-commercial purposes. Commercial purposes, please contact me.
 * 
 * This class is where most of the work is done in the Huffman
 * Code package.
 * 
 * The BinaryTree works with Nodes in the Node Class and
 * is used to structure relationships between nodes.
 * 
 * A lot is written about this in the README.md.
 */
class BinaryTree
{
    /* CONFIG */
    const BUILD_TREE_MAX_LOOPS = 50;//Set this to avoid run away loops or recursions
    /* ENDCONFIG */

    private $tree;//this tree

    private $codes;//array: Node names => binary codes

    private $alpha;//alphabet set by Huffman Class to assist in setting $codes (next line above)

    private $nodes_array = array();//used when building tree

    function __construct() {
        //nothing here
    }
    
    /**
     * get the tree
     * @return: $this->tree
     */
    public function getTree(): ?Node {
        return $this->tree;
    }

    /**
     * set the tree
     * @param: $tree: the tree of type Node
     * @return: bool
     */
    private function setTree( ?Node $tree ): bool {
        if(isset( $tree )) {
            $this->tree = $tree;
            return true;
        }
        return false;
    }

    /**
     * sets the alphabet:
     * @param: array $alpha - Each element is [Letter => binary code]
     * @return: void
     * The alphabet array is used to encode the message.
     * We could have used the $codes array, but it has
     * the non alphabet parent nodes that are useless
     * for the encoding. We are basically masking
     * the $codes array with the alphabet array.
     */
    public function setAlpha( array $alpha ): void {
        $this->alpha = array();
        foreach($alpha as $name => $value) {
            if(isset($this->codes[$name])) {
                $this->alpha[$name] = $this->codes[$name];
            }
        }
    }

    /**
     * get the alphabet
     * @return: assoc array
     */
    public function getAlpha(): array {
        return $this->alpha;
    }
    
    /**
     * get the codes array
     * @return: assoc array
     * these codes are set while building the tree, so there is no setter.
     */
    public function getCodes(): array {
        return $this->codes;
    }

    /**
     * create parent node
     * @param: Node $left_node
     * @param: Node $right_node
     * @return: Node $parent_node
     * Combine two trees/nodes by creating a parent and "pointing"
     * to left child and right child. It's value is the sum of the
     * child values. It's name is the concat of child names.
     */
    public function createParentNode( ?Node $left_node, ?Node $right_node): Node {
        $parent_name = $left_node->name() . $right_node->name();
        $parent_value = $left_node->value() + $right_node->value();
        $parent_node = new Node( $parent_name, $parent_value, $left_node, $right_node, $sbin = '');
        return $parent_node;
    }

    /**
     * Build the Tree
     * @param: array of Node $nodes
     * @return: bool
     * Send an array of Primitive Nodes.
     * A Primitive Node has a name, value, null children, optional bin value
     * The array must be ordered already by value ascending.
     */
    public function buildTree( array $nodes = null ): bool {
        $loops = 0;
        while( count($nodes) >= 2 && $loops++ < self::BUILD_TREE_MAX_LOOPS ) {
            $left = array_shift($nodes);//pop left node off the front
            $right = array_shift($nodes);//pop right node off the front

            $parent_node = $this->createParentNode( $left, $right );

            $nodes = $this->insertNode( $nodes, $parent_node );

        }
        return $this->setTree($nodes[0]);
    }
    /**
     * Inserts a node into the ordered array, keeping it ordered.
     * @param: array of Node $nodes
     * @param: Node $n
     * @return: array Node $nodes
     * This technique saves us from having to sort the array
     * based on objects values, which wouldn't be easy.
     */
    public function insertNode( array $nodes, Node $n): array {
        if(count($nodes) == 0) {
            return array($n);
        }

        $front = array();
        $back = $nodes;
        $loops = 0;
        $j = 0;
        /**
         * While Loop
         * Fast Forwards to position where we want to insert the node.
         * Right above ^ you see $bask = $nodes. We'll shift values off
         * that onto the $front array until $n->value is between
         * values of $front and $back arrays. Then the loop breaks and
         * we'll insert $n below. Then push on the rest of the $back array,
         * if any.
         */
        while( count($back) > 0 && $loops < self::BUILD_TREE_MAX_LOOPS) {
            $loops += 1;

            //Fast forward to insertion point between $front and $back arrays
            if($n->value() > $back[0]->value()) {
                $front[$j++] = array_shift($back);
            }
            else {
                break;
            }
        }
        /* Here we insert the Node $n */
        $front[$j++] = $n;

        /* Push on the rest of the $back array, if any */
        if(count($back) > 0) {
            while(count($back) > 0) {
                $front[$j++] = array_shift($back);
            }
        }
        return $front;
    }

    /**
     * Set the binary codes for each node in tree
     * @param: Node $tree
     * @param: string $bin (binary code)
     * @return: Node
     * Walk the tree recursively, setting the binary codes for each node
     * by building that code with 0s and 1s or left and right.
     */
    public function setBinaryCodes( ?Node $tree , string $bin = '' ): ?Node {
        if( $tree == null ) {
            return null;
        }
        $tree->setBin($bin);
        /**
         * We build codes array as well, since it's easy to do here.
         * This is just a little side show. 
         */
        $this->codes[$tree->name()] = $bin;

        /**
         * Back to business.
         */
        if( is_object($tree->left())) {
            $lbin = $bin . '0';
            //RECURSION
            $left = $this->setBinaryCodes( $tree->left(), $lbin);
            //Set the left node to the result of recursion
            $tree->setLeft($left);
        }

        if( is_object($tree->right())) {
            $rbin = $bin . '1';
            //RECURSION
            $right = $this->setBinaryCodes( $tree->right(), $rbin);
            //Set the right node to the result of recursion
            $tree->setRight($right);
        }  

        return $tree;
    }
}