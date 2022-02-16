#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <math.h>

char *add_long(char *a, int lena, char *b, int lenb);

int main(int argc,char *argv[])
{
        if(argc != 3)
        {        printf("Usage: longsum [int] [int]\n");
                 exit(0);
        }
	/* get strlen of argvs for variable length a and b arrays */
	int lena = strlen(argv[1]), lenb = strlen(argv[2]), lenc;
	if(lena >= lenb) { lenc = lena + 1; }
	else { lenc = lenb + 1; }

	char a[lena], b[lenb], sum[lenc], *s;
        register int m, n;

	for(m=0,n=0;argv[1][m];m++,n++) { a[n]=argv[1][m]; }
        for(m=0,n=0;argv[2][m];m++,n++) { b[n]=argv[2][m]; }
        s = add_long(a,lena,b,lenb);
 	
	/* skip over leading zeros */
	for(m=0; *(s+m) == '0'; m++) ;
	
	for(n=0,m;*(s+m);m++,n++) sum[n] = *(s+m);
	
	sum[m] = '\0';
	printf("%s\n",sum); 	
        return 0;
}

char *add_long(char *a, int lena, char *b, int lenb)
{
	int lenc;
	if(lena >= lenb) { lenc = lena + 1; }
        else { lenc = lenb + 1; }
        char arev[lena], brev[lenb], sumrev[lenc], *s, sum[lenc];
        int i, j, m, n, carry=0, temp, tempa, tempb;
        
        for(i=lena-1, j=0;i>=0;i--, j++)
        {
                arev[j] = *(a+i);
        }
        for(i=lenb-1, j=0;i>=0;i--, j++)
        {
                brev[j] = *(b+i);
        }
	
        for(m=0;m<(lenc - 1);m++)
        {
		if(m<lena) { tempa = arev[m] - 48; }
		else tempa = 0;
		if(m<lenb) { tempb = brev[m] - 48; }
		else tempb = 0;
		temp = tempa + tempb + carry;
                carry = temp / 10;
		sumrev[m] = (temp % 10) + 48;	
	}
	if(carry > 0) { sumrev[m] = carry + 48; }
	else m--;
	for(n=0;m>=0;m--,n++) { sum[n] = sumrev[m]; }
	sum[n] = '\0';
        s = sum;
        return(s);
}


