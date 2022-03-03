Huffman Class
Generate Huffman Codes of Messages

02/03/2022
LICENSING
As long as you leave credit to the author, John Wenzel and his email untouched, 
you are free to use for non-commercial purposes. Commercial purposes, please contact me.

    Tested on PHP 7.3.31

Author: John Wenzel johngwenzel@gmail.com
https://github.com/jgwenzel/jgw

    Files in package:
        Huffman.class.php
        BinaryTree.class.php
        Node.class.php
        huffmanencode.php (optional page with example)

The Huffman class is for generating Huffman Code for a given message.
The is done most simply as:

$huff = new Huffman("HUFFMAN CODES ARE FUN MAN");

    $huff->printCode();
 
    output-> 11000110101001010100111000011001111101111110111110000011111011011000101101100001010011100

Other functions to call are:

    $huff->printMessage(); //Print the message you sent after being filtered

    $huff->printAlphabet(); //Print the Alphabet and Letter frequencies of the message.

    $huff->printCodes(); //Print the Alphabet and Binary Codes assigned each Letter.

    $huff->printCode(); //Print the Huffman code of the message.

If you just want unformatted results, see the get functions in Huffman.class.php like:

    $huff->getCode():

    $huff->getAlphabet();

    $huff->getCodes();

    etc.

Now for something completely different.

JK. This is the same stuff.

WHAT'S A HUFFMAN CODE?
A Huffman Code is a code "commonly used for lossless data
compression" (Wikipedia). It was developed in 1952 by David
Huffman at MIT. He called them "Minimum Redundancy Codes".

        At its root, Huffman Codes use a frequency-sorted binary tree.

For compressing messages by singular symbols, Huffman Coding 
is optimal. Wikipedia states there are other methods
using non-single symbol methods that are better. Namely
Arithmetic Coding and Asymmetrical Numeral Systems. 

BINARYTREE CLASS in more depth.
A tree is formed by taking a node, then setting it's left
and right attributes to equal other nodes, so there is a nested
relationship. It becomes objects within objects with objects...
or Nodes within Nodes within Nodes...
 
    See the Nodes class to understand their simple structure.
 
I use the word Tree and Node almost interchangeably because
a Tree is just a node where it's children (left and right) may
be nodes too. And you may take a Node from the tree and do
something with it, but you are likely handling a subtree of
the whole tree.

The challenging part of working with nested objects is that
you can't iterate through them easily, like say to search for
a value. At least, not easily like with arrays. But similar
methods can be made for object trees.

About buildTree() LOOPING with ARRAYS of NODES
While building the tree, looping with arrays of objects works
well. In buildTree(), we start with an array of childless Nodes,
that I refer to as Primitive Nodes. We begin with the Node Queue
which has one primitive Node for each Letter, each with the 
Node::value() set. The value is the frequency of the Letter in 
the Message. In each LOOP, we:

    1) take the first 2 Nodes off the front of the Queue array
       using array_shift().
    2) make them a parent and attach them as children. (left and
       right) We use createParentNode() to do so. The names of
       the children are concatenated (A and B become AB) for the
       parent's name and sum their values for the parent's value.
    3) Then back inside buildTree(), we add this parent to the 
       Node Queue in a special way so we don't have to sort the
       array of Nodes. Using insertNode(), we loop through the queu
       until the parent node's value is in the correct place in 
       the ascending array, then insert it there. As we loop through,
       each time we are taking two children off, and putting one
       parent (with to children) on, hence the queue is shortened
       by one each time. Eventually, the entire queue is aggregated
       into a single tree in an array with one element. We "pop" it
       out of the array and return just the tree. Pretty Nifty!
     
About setBinaryCodes() and RECURSION
Now we have built the tree, and no longer have an array to
zip through. Enter recursion! How we love recursion! It's usually
a puzzle, and this did not disappoiht. Here are the steps that
take place in this recursive function.
 
    1) We initialize it by calling it with the Tree and the binary
       code = ''.
    2) In the function, we set the Node::value equal to the binary
       code.
    3) for Left and Right, I'll just explain the left.

       On the left side, if it exists, we add a '0' onto the bin
       which we set, and then we set the entire left node to the
       value of the function recursively. Each time around it will
       be recursing on the left subtree of the parent that was sent.
       Once all those subtrees reach the bottom where the trees are
       null, the entire left tree percolates up and that becomes the
       left node of the first tree we sent.

    4) Done!

NOTE:
Recursion is not so scary here, but when I've programmed with C, it can
overrun your memory quickly and give you segfaults. I've gotten in the
habit of creating loop constraints. You can set those CONFIG values in
the BinaryTree class and the Huffman class. Still, I'm always ready to hit 
ye old ctrl-c.
  


If you dig geekin' an' tweakin' out on the Maths too, drop me a note!
John Wenzel johngwenzel@gmail.com

Git on an' git my github, kee kee!: https://github.com/jgwenzel/jgw

Rome was not built in a days.
