/* code by John Wenzel johngwenzel@gmail.com */
#include <stdlib.h>
#include <stdio.h>

int main(int argc,char *argv[])
{       if(argc == 1)
        {      printf("Problem: You must enter an integer argument following fibonacci\n");
               exit(1);
        }
        
        if(argc != 2)
        {
               printf("Problem: You must execute like so:\n\nfibonnacci [integer]\n\n");
               exit(1);
        } 

        int num_fib;
        sscanf(argv[1],"%d",&num_fib);
        int fib[num_fib];
        int i;

        fib[0] = 0;
        fib[1] = 1;

        for(i = 2; i < num_fib; i++)
                fib[i] = fib[i-1] + fib[i-2];

        for (i = 0; i < num_fib; i++)
                printf("%3d   %6d\n", i, fib[i]);
}
