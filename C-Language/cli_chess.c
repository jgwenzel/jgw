/*********************************
* This is Chess by John Wenzel   *
* johngwenzel@gmail.com          *
* Written in  July of 2004       *
*********************************/

#include <stdio.h>
#include <string.h>
#include <math.h>
#include <stdlib.h>
#include <time.h>
#include <ctype.h>

/*************************************************************/
/* function prototypes - (line numbers may not be up to date)*/
                                          /* main is line 54 */
float compare_players( char whosturn );
int pick_move( int method, int whosmove );
int make_potential_moves( void );          /* line 116 */
int promote_pawn(int p, int *pto );
int move_piece(int *pfrom, int *pto );           /* line 116 */
int is_in_check( char color );
int is_draw( void );
int is_in_threat( int r, int f, char color );
int build_moves( void );
int count_moves( int ncolor );
int display_board( char perspective, int showpotmovesfor );           /* line 127 */
int comp_move( void );
int new_piece( char ch, char color );
int initialize( void );                   /* line 207 */
int random_number( int limit );
/*************************************************************/

    /* GLOBALS */
    char whosturn = 'W';
    int whosmove = 1;
    int computer_is_thinking = 0;
    /* player structure */
    struct plyr
    {
    	char color;
    	char type;
    	char name[11];
    } player[2];
    
    /* piece structure */
    struct pce
    {
        char lname[11];
        char sname[3];
        char color;
        char relmov[15][15][2];
        char potmov[10][10][2];
        float mob;
    } pc[48];
        /* the preceding '48' accounts for 32 pieces and the exception that 16 pawns got promoted. I'm being extra careful */

    /* move structure */
    struct mve
    {
        int from[2];
        int to[2];
        float val;
        int take;
    } mov[2][200];
    
    /* history structure and count - hisnum */
	struct hstry
	{
		int pc;
		int from[2];
		int to[2];
		char act;
		char thispc[11];
		char takespc[11];
	} his[1000];
	
	int hisnum = 0;
	int enpass[4] = { 0,0,0,0 };

    
    /*********************************************************************************************************
    * initialize board                                                           *
    * 1)rank and file of 0 or 9 are out of bounds so i'll give them all values of -1                         *
    * 2)blank squares will recieve a value of 50 (the max index for pieces is 47)                *
    * 3)all others receive the value according to the index of the piece that occupies it (0-31)             *
    * 4)it is built 'upside down' since i want [1][1] to represent the whites left rook                  *
    *********************************************************************************************************/ 
    int board[10][10];

int main( void )
{
    char perspective = 'W',input[10];
    int mode = 1, intresult, temp1, temp2, instructions = 1,i,j,k,m,n,stime, method,r,f;
    long ltime;
    int from[2][1] = {2,2};
    int to[2][1] = {4,2};
    int *pfrom,*pto;
    
    int onummoves, dnummoves, bestmove, omovnumber, dmovnumber;
    float oadvantage, dadvantage, lastdadvantage, tempadvantage;
    char savedwhosmove;
   	int savedboard[10][10], savedhisnum, savedwhosturn, savedenpass[4];
    struct pce savedpc[48];       
    struct hstry savedhis[1000];
    struct mve savedmov[2][200];
    
    char savedwhosmove2;
   	int savedboard2[10][10], savedhisnum2, savedwhosturn2, savedenpass2[4];
    struct pce savedpc2[48];       
    struct hstry savedhis2[1000];
    struct mve savedmov2[2][200];
    
    char savedwhosmove3;
   	int savedboard3[10][10], savedhisnum3, savedwhosturn3, savedenpass3[4];
    struct pce savedpc3[48];       
    struct hstry savedhis3[1000];
    struct mve savedmov3[2][200];
    
    while( mode > 0)
    {
        if(mode == 1)
        {
            printf("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n                               WELCOME TO MY CHESS GAME\n                                    programmed in C\n                                    by John Wenzel\n\n\n\n\n\n\n\n\n\n\n\n"); 
            /* printf("What color would you like, (B)lack or (W)hite?: "); */
            printf("GAME TYPES:\n");
            printf("__________________________________________\n");
            printf("Player1 is White and Player2 is Black:   1\n");
            printf("Player1 is White and Computer is Black:  2\n");
            printf("Player1 is Black and Computer is White:  3\n");
            printf("Computer is White and Computer is Black: 4\n");
            printf("__________________________________________\n");
            printf("PICK A GAME TYPE: ");
            scanf("%s",&input);
            /*
            if(input[0] == 'B')
            {   player1 = 'B';
                player2 = 'W';
            }
            else
            {   player1 = 'W';
                player2 = 'B';
            }
            */
         	player[1].color = 'W';
            player[0].color = 'B';
            if(input[0] == '1')
            {
            	player[1].type = 'H';
            	player[0].type = 'H';
            	strcpy(player[1].name,"HUMAN");
            	strcpy(player[0].name,"HUMAN");
            }
            else if(input[0] == '2')
            {
            	player[1].type = 'H';
            	player[0].type = 'C';
            	strcpy(player[1].name,"HUMAN");
            	strcpy(player[0].name,"COMPUTER");
            }
            else if(input[0] == '3')
            {
            	player[1].type = 'C';
            	player[0].type = 'H';
            	strcpy(player[1].name,"COMPUTER");
            	strcpy(player[0].name,"HUMAN");
            	perspective = 'B';
            }
            else
            {
            	player[1].type = 'C';
            	player[0].type = 'C';
            	strcpy(player[1].name,"COMPUTER");
            	strcpy(player[0].name,"COMPUTER");
            	instructions = 3; /* NONE */
            }
            initialize();
            make_potential_moves( );
            build_moves( );
            display_board( perspective, -1 );
            mode = 2;
        } /* end of mode 1 */
      
        if(mode == 2)
        {
            if(instructions == 1) {
                printf("(Q)uit | (S)witch View | (N)ew Game\n");
                printf("Enter moves like so 'B2B3'\n"); 
                printf("To see potential moves for 'B2', enter 'PB2'\n");
                printf("Turn off these (I)nstructions\n\n");
            }
            else if(instructions == 0) {
                printf("(I)nstructions\n");
            }
            else if(instructions < 0) {
                instructions+=2;
            }
            /* if(player[whosmove].type != 'C')
            { */
            	intresult = is_draw();
            	if(intresult == 1)
            	{
            		printf("\n**************\n*   DRAW!    *\n**************\n");
            		printf("Fifty moves have occurred without a capture or the moving of a pawn\n");
            		mode=3;
            	}
            	else if(intresult == 2)
            	{
            		printf("\n**************\n*   DRAW!    *\n**************\n");
            		if( whosturn == 'W' )
            			printf("Black has moved the same piece to the same position 3 times in a row\n");
            		else
            			printf("White has moved the same piece to the same position 3 times in a row\n");
            		mode=3;
            	}
            /* } */
        }
        
        if(mode==2)
        {
            if( is_in_check( whosturn ) )
            {
                if( count_moves( whosmove ) == 0 )
                {
                    if( whosturn == 'W')
                        printf("\n**************************\n* CHECKMATE! Black Wins! *\n**************************\n");
                    else
                        printf("\n**************************\n* CHECKMATE! White Wins! *\n**************************\n");
                    mode = 3;
                }
                else
                {
                    if(whosturn == 'W') printf("White, YOU'RE IN CHECK: ");
                    else printf("Black, YOU'RE IN CHECK: ");
                }
            }
            else
            {
                if( count_moves( whosmove ) == 0 )
                {           
                    printf("\n**************\n* STALEMATE! *\n**************\n");
                    mode = 3;
                }
                else
                {
                    if(whosturn == 'W') printf("White make your move: ");
                    else printf("Black, make your move: ");
                }
            }
        }
        
        /* get input */
        if(mode == 2)
        {
        	
            if(player[whosmove].type == 'H')
            {
                scanf("%s",&input);
                for(i=0;input[i];i++) input[i] = toupper( input[i] );
            }
            else
            {	 
                printf("%s is moving\n",player[whosmove].name);
                input[0] = '0';
            }
            
            
            if(input[0] == 'Q') 
            {
                printf("See You Later!\n\n");
                mode=0;   
            }
            else if(input[0] == 'I') 
            {
                if(instructions == 1) instructions = 0;
                else instructions = 1;
                display_board( perspective, -1 );
            } 
            else if(input[0] == 'S') 
            {
             	if(perspective == 'W') perspective = 'B';
                else perspective = 'W';
                printf("Switched Board View\n");
                display_board( perspective, -1 );   
            }
            else if(input[0] == 'N') 
            {
                mode = 1;
            }
            else if(input[0] == 'P') 
            {
                temp1 = input[1] - 64;
                temp2 = input[2] - 48;
                if((temp1 > 0) && (temp1 < 9) && (temp2 > 0) && (temp2 < 9) && (board[temp2][temp1] != -1))
                { 
                    make_potential_moves( );
                    build_moves();
                    display_board( perspective, board[temp2][temp1]);
                }
                else
                {
                    printf("Sorry: Bad Entry, Try Again\n");
                    instructions-=2;
                } 
            }
            else 
            { 
            	/* GET MOVE INPUT */
                if(player[whosmove].type == 'H')
                {
                	
                    from[1][0] = input[0] - 64;
                    from[0][0] = input[1] - 48;
                    to[1][0] = input[2] - 64;
                    to[0][0] = input[3] - 48;
                    
                }
                else
                {

                	/* COMPUTER'S MOVE */ 
                	/* SAVE STATE OF GAME TO REVERT TO AFTER COMPUTER PICKS MOVE */
    				savedwhosmove = whosmove;
    				savedwhosturn = whosturn;
    				savedhisnum = hisnum;
                	for(r=0;r<10;r++) for(f=0;f<10;f++) savedboard[r][f] = board[r][f];
     				for(i=0;i<48;i++) savedpc[i] = pc[i];
 					for(i=0;i<1000;i++) savedhis[i] = his[i];
     				for(r=0;r<2;r++) for(i=0;i<200;i++) savedmov[r][i] = mov[r][i];
     				for(i=0;i<4;i++) savedenpass[i] = enpass[i];
     					
     				onummoves = count_moves( whosmove );
     				bestmove = 0;
     				lastdadvantage = 1000;
     				computer_is_thinking = 1;
     			  	for(j=0;j<onummoves;j++)
     			  	{
     					/* OFFENSE #1 MOVE */
						from[1][0] = mov[whosmove][j].from[1];
                		from[0][0] = mov[whosmove][j].from[0];
                		to[1][0] = mov[whosmove][j].to[1];
                		to[0][0] = mov[whosmove][j].to[0];
                		pfrom = &from[0][0];
                		pto = &to[0][0];
                		/* printf("HERE? %d: %d,%d => %d,%d\n",j,from[0][0],from[0][1],to[0][0],to[0][1]); */
                	
                		if(hisnum > 999)
                		{
                			printf("hisnum overrun\n\n");
                			break;
                		}
                		else intresult = move_piece( pfrom, pto );
                		
                		if((intresult == 1) || (intresult == 2)) 
                		{
                		    if(intresult == 2)
                    		{
                    			i = new_piece( 'Q', whosturn);
                    			promote_pawn( i, pto );
                    		}
                    	}
                    	else printf("BADMOVE OFFENSE MOVE: %d\n",intresult);
                    
                    	if(whosturn == 'W') whosturn = 'B';
                    	else whosturn = 'W';
                    	if(whosmove == 1) whosmove = 0;
                    	else whosmove = 1;
                    	make_potential_moves( );
                    	build_moves();
                    	
                    	
                    	/* DEFENSE #1 MOVE */
                    	savedwhosmove2 = whosmove;
    					savedwhosturn2 = whosturn;
    					savedhisnum2 = hisnum;
                		for(r=0;r<10;r++) for(f=0;f<10;f++) savedboard2[r][f]=board[r][f];
     					for(i=0;i<48;i++) savedpc2[i] = pc[i];
 						for(i=0;i<1000;i++) savedhis2[i] = his[i];
     					for(r=0;r<2;r++) for(i=0;i<200;i++) savedmov2[r][i] = mov[r][i];
     					for(i=0;i<4;i++) savedenpass2[i] = enpass[i];
     					dnummoves = count_moves( whosmove );
     					dadvantage = -1000.00;
     					
                    	for(k=0;k<dnummoves;k++)
                    	{
                    		from[1][0] = mov[whosmove][k].from[1];
                			from[0][0] = mov[whosmove][k].from[0];
                			to[1][0] = mov[whosmove][k].to[1];
                			to[0][0] = mov[whosmove][k].to[0];
                			pfrom = &from[0][0];
                			pto = &to[0][0];
                			if(hisnum > 999)
                			{
                				printf("hisnum overrun in k\n");
                				break;
                			}
                			else
                				intresult = move_piece( pfrom, pto );
                			if((intresult == 1) || (intresult == 2)) 
                			{
                			    if(intresult == 2)
                    			{
                    				i = new_piece( 'Q', whosturn);
                    				promote_pawn( i, pto );
                    			}
                    		}
                    		else
                    			printf("BADMOVE DEFENSE MOVE: %d\n",intresult);
                    			
                    		make_potential_moves( );
                    		build_moves( );
                    		/* display_board( perspective, -1); */
                    		tempadvantage = compare_players( whosturn );
  							if( tempadvantage > dadvantage)
                			{ 	/* find best defensive advantage for worst case scenario */
                				dadvantage = tempadvantage;
                			}
							if(whosturn == 'W') whosturn = 'B';
                    		else whosturn = 'W';
                    		if(whosmove == 1) whosmove = 0;
                    		else whosmove = 1;
	
                			whosmove = savedwhosmove2;
    						whosturn = savedwhosturn2;
    						hisnum = savedhisnum2;
                			for(r=0;r<10;r++) for(f=0;f<10;f++) board[r][f]=savedboard2[r][f];
     						for(i=0;i<48;i++) pc[i] = savedpc2[i];
 							for(i=0;i<1000;i++) his[i] = savedhis2[i];
     						for(r=0;r<2;r++) for(i=0;i<200;i++) mov[r][i] = savedmov2[r][i];
     						for(i=0;i<4;i++) enpass[i] = savedenpass2[i];
                			
                    	} /* END DO k LOOP */
                    	/* dadvantage has denotes the best defensive move factor */
                		if( dadvantage < lastdadvantage)
                		{	/* is offensive advantage greater than d */
                			lastdadvantage = dadvantage;
                			bestmove = j;
                			/* printf("BESTMOVE %d\n",bestmove); */
                			/* printf("DADVANTAGE: %f\n\n",dadvantage); */
                		}
                	
                		whosmove = savedwhosmove;
    					whosturn = savedwhosturn;
    					hisnum = savedhisnum;
                		for(r=0;r<10;r++) for(f=0;f<10;f++) board[r][f] = savedboard[r][f];
     					for(i=0;i<48;i++) pc[i] = savedpc[i];
 						for(i=0;i<1000;i++) his[i] = savedhis[i];
     					for(r=0;r<2;r++) for(i=0;i<200;i++) mov[r][i] = savedmov[r][i];
     					for(i=0;i<4;i++) enpass[i] = savedenpass[i];
                  		/* display_board( perspective, -1 ); */
                  	} /* END OF j LOOP */
                	/* i = pick_move( 2 , whosmove ) */
                	/* printf("BEST MOVE: %d\n",bestmove); */
                	from[1][0] = mov[whosmove][bestmove].from[1];
                	from[0][0] = mov[whosmove][bestmove].from[0];
                	to[1][0] = mov[whosmove][bestmove].to[1];
                	to[0][0] = mov[whosmove][bestmove].to[0];
                		
                } /* END OF COMPUTER'S MOVE */
                computer_is_thinking = 0;
                
                /* printf("%c MOVE: %d%d-%d%d\n",whosturn, from[0][0],from[1][0],to[0][0],to[1][0]); */
                pfrom = &from[0][0];
                pto = &to[0][0];    
                if(hisnum > 999)
                	intresult = 200;
                else
                	intresult = move_piece( pfrom, pto );
                if((intresult == 1) || (intresult == 2)) 
                {
                    if(intresult == 2)
                    {
                    	if(player[whosmove].type == 'H')
                    	{
                    		strcpy(input,"N");
                    		while(!((input[0] == 'Q') || (input[0] == 'R') || (input[0] == 'K') || (input[0] == 'B')))
                    		{
                    			printf("Promote Pawn to (Q)ueen, (R)ook, (K)night, or (B)ishop?:");
                    			scanf("%s",&input);
                    			for(j=0;input[j];j++) input[j] = toupper( input[j] );
                    		}
                    	}
                    	else input[0] = 'Q'; /* computer picks queen */
                    	
                    	i = new_piece( input[0], whosturn);
                    	promote_pawn( i, pto );
                    }
                    
                                        /* printf("NUMBER OF MOVES: %d\n", count_moves( whosmove ) ); */
                    if(whosturn == 'W') whosturn = 'B';
                    else whosturn = 'W';
                    if(whosmove == 1) whosmove = 0;
                    else whosmove = 1;
                    make_potential_moves( );
                    build_moves();
                    display_board( perspective, -1 );

                }
                else if(intresult == 102) 
                {
                    printf("You can't move the opponents pieces! Try Again\n");
                    instructions-=2; 
                }
                else if(intresult == 103) 
                {
                    printf("Sorry, that's an illegal move. Try Again\n");
                    instructions-=2;				
                }
                else if(intresult == 200) 
                {
                    printf("Sorry, You've reached the 1000 move limit\n");
                    instructions-=2;
                }
                else 
                {
                    printf("Sorry: Bad Entry, Try Again\n");
                    instructions-=2;
                }
                
                if((intresult > 99) && (player[whosmove].type == 'C'))
                {
                	printf("COMPUTER HAS MADE THIS ERROR IN MOVING\n");
                	mode = 3;
                }
            } 
        }/* end of mode 2 */
        
        if(mode == 3)
        {
            printf("\nPlay Again? Enter (Y)es or (N)o : ");
            scanf("%s",&input);
            if(input[0] == 'Y') mode=1;
            else
            {
                printf("See You Later!\n\n");
                mode=0;
            }
        } 
        
    }
    
        	hisnum=0;
        	while((his[hisnum].act == 'T') || (his[hisnum].act == 'V') || (his[hisnum].act == 'E') || (his[hisnum].act == 'P') || (his[hisnum].act == 'Q') || (his[hisnum].act == 'C'))
        	{
        		if(his[hisnum].act == 'T')
        			printf("%d: %s at %d,%d takes %s at %d,%d\n",hisnum,his[hisnum].thispc,his[hisnum].from[0],his[hisnum].from[1],his[hisnum].takespc,his[hisnum].to[0],his[hisnum].to[1]);
        		else if(his[hisnum].act == 'V')
        			printf("%d: %s at %d,%d moves to %d,%d\n",hisnum,his[hisnum].thispc,his[hisnum].from[0],his[hisnum].from[1],his[hisnum].to[0],his[hisnum].to[1]);
        		else if(his[hisnum].act == 'E')
        			printf("%d: %s at %d,%d en passants %s while moving to %d,%d\n",hisnum,his[hisnum].thispc,his[hisnum].from[0],his[hisnum].from[1],his[hisnum].takespc,his[hisnum].to[0],his[hisnum].to[1]);
        		else if(his[hisnum].act == 'P')
        			printf("%d: %s at %d,%d moves to %d,%d and is promoted\n",hisnum,his[hisnum].thispc,his[hisnum].from[0],his[hisnum].from[1],his[hisnum].to[0],his[hisnum].to[1]);
        		else if(his[hisnum].act == 'Q')
        			printf("%d: %s at %d,%d takes %s at %d,%d and is promoted\n",hisnum,his[hisnum].thispc,his[hisnum].from[0],his[hisnum].from[1],his[hisnum].takespc,his[hisnum].to[0],his[hisnum].to[1]);        		
        		else if(his[hisnum].act == 'C')
        			printf("%d: CASTLE\n",hisnum);

        		hisnum++;
        	}   
    exit(1);
}

float compare_players( char whosturn )
{

	int p,r,f,onummoves,dnummoves;
	float offval = 0.0 , defval = 0.0 , advantage;
	
	/* 
	for(r=1;r<=8;r++)
	{
		for(f=1;f<=8;f++)
		{
			p = board[r][f];
			if((p > -1) && (p < 48))
			{
				if(pc[p].color == whosturn) offval += pc[p].mob;
				else defval += pc[p].mob;
			}
		}
	}
	advantage = offval - defval;
	*/
	int ocolor, dcolor;
	if( whosturn == 'W')
	{
		ocolor = 1;
		dcolor = 0;
	}
	else
	{
		ocolor = 1;
		dcolor = 0;
	}
	onummoves = count_moves( ocolor );
	dnummoves = count_moves( dcolor );
	/* printf("%d NUMMOVES: %d\n",ocolor,onummoves); */
	/* printf("%d NUMMOVES: %d\n",dcolor,dnummoves); */
	advantage = onummoves - dnummoves;
	/* printf("advantage: %f\n",advantage); */

	
	return advantage;
	
}

int pick_move( int method, int ncolor )
{
	int i=0,j;
	float v;
	/* time_t start;
	
	start = time( NULL );
	
	while(difftime( time( NULL ), start ) < 0.2);
	*/
	int nummoves = count_moves( ncolor );
	
	if( method == 1) /* RANDOM */
	{
    	i = random_number( nummoves );
	}
	else if( method == 2 ) /* HIGHEST VALUE */
	{
		for(j=1,i=0,v=mov[ncolor][0].val;j<nummoves;j++)
		{
			if(mov[ncolor][j].val > v)
			{
				v = mov[ncolor][j].val;
				i = j;
			}
		}
		
		if(mov[ncolor][i].val == 0.25) i = pick_move( 1 , ncolor);
    }
    return i;
}

int count_moves( int ncolor )
{
    int i=0;
    for( ;((i<=199) && (mov[ncolor][i].from[0] > 0));i++);
    return i;
}

int build_moves( )
{
    /* mov[2][200] 
       mov[0][ ] is black
       mov[1][ ] is white */
    int p,t,rank,file,r,f,i,j,color,bp=0,wp=0;
    
    /* wipe the array */
    for(i=0;i<2;i++)
    {
        for(j=0;j<200;j++)
        {   
            mov[i][j].from[0] = -1;
            mov[i][j].from[1] = -1;
            mov[i][j].to[0] = -1;
            mov[i][j].to[1] = -1;
            mov[i][j].take = -2;
            mov[i][j].val = 0;
        }
    }
        for(rank=1;rank<=8;rank++)
        {
            for(file=1;file<=8;file++)
            {
                p=board[rank][file];
                if((p>-1) && (p<48))
                {   
                    for(r=1;r<=8;r++)
                    {
                        for(f=1;f<=8;f++)
                        {
                            if((pc[p].potmov[r][f][0] == 'X') || (pc[p].potmov[r][f][0] == 'K') || (pc[p].potmov[r][f][0] == 'V'))
                            {
                            	if( !((hisnum>7) && (p == his[hisnum-4].pc) && (r == his[hisnum-4].to[0]) && (f == his[hisnum-4].to[1]) && (p == his[hisnum-8].pc) && (r == his[hisnum-8].to[0]) && (f == his[hisnum-8].to[1])) )
                                {
                                	t = board[r][f];
                            	
                                	if(pc[p].color == 'W')
                                	{
                                	    mov[1][wp].from[0] = rank;
                                	    mov[1][wp].from[1] = file;
                                	    mov[1][wp].to[0] = r;
                                	    mov[1][wp].to[1] = f;
                                	    mov[1][wp].take = board[r][f];
                                	    if(t > -1) mov[1][wp].val = ( pc[t].mob - pc[p].mob );
                                	    else mov[1][wp].val = 0.25;
                                	    wp++;
                                	}
                                	else if(pc[p].color == 'B')
                                	{
                                	    mov[0][bp].from[0] = rank;
                                	    mov[0][bp].from[1] = file;
                                	    mov[0][bp].to[0] = r;
                                	    mov[0][bp].to[1] = f;
                                	    mov[0][bp].take = board[r][f];
                                	    if(t > -1) mov[0][bp].val = ( pc[t].mob - pc[p].mob );
                                	    else mov[0][bp].val = 0.25;
                                	    bp++;
                                	}
                               }
                               /* else
                                	printf("over three moves\n"); */
                            }
                        }
                    }
                }
            }
        }
    /*
    for(r=0;r<count_moves( 0 );r++) printf("%f\n",mov[0][r].val);
    printf("\n\n");
    r=0;
    for(r=0;r<count_moves( 1);r++) printf("%f\n",mov[1][r].val);
    */
    
    return 1;
}

int is_in_check( char color )
{
	
    int kings_number,kings_rank=0,kings_file=0,piece_number,rank,file,r,f;
    if(color == 'W') kings_number = 4;
        else if(color == 'B') kings_number = 28;
    rank=1; 
    /* get kings rank and file */ 
    while((rank<=8) && (kings_file<1))
    {   for(file=1;file<=8;file++)
        {
            if(board[rank][file] == kings_number)
            {
                kings_rank = rank;
                kings_file = file;
            }   
        }
        rank++;
    }
    
    for(rank=1;rank<=8;rank++)
    {
        for(file=1;file<=8;file++)
        {
            piece_number = board[rank][file];
            if((piece_number > -1) && (piece_number < 48) && (pc[piece_number].color != color))
            {
            
                if((pc[piece_number].potmov[kings_rank][kings_file][0] == 'K') || (pc[piece_number].potmov[kings_rank][kings_file][0] == 'Y'))
                {
                	/* if(computer_is_thinking == 1)
					{
						printf("is_in_check came up\n");
						printf("White nummoves: %d\n", count_moves( 1 ));
						printf("Black nummoves: %d\n", count_moves( 0 ));
					} */
					
                	return 1;
                }
            
            }   
        }
    }
    return 0;
}

int is_in_threat( int r, int f, char color )
{
	int rank, file, piece_number;
	for(rank=1;rank<=8;rank++)
    {
        for(file=1;file<=8;file++)
        {
            piece_number = board[rank][file];
            if((piece_number > -1) && (piece_number < 48) && (pc[piece_number].color != color))
            {
                if((pc[piece_number].potmov[r][f][0] == 'K') || (pc[piece_number].potmov[r][f][0] == 'Y') || (pc[piece_number].potmov[r][f][0] == 'X')) return 1;
            }   
        }
    }
    return 0;
}

int is_draw( void )
{
	int m = (hisnum - 1),n,o;
	if(computer_is_thinking) return 0;
	/* Has player moved the same piece to the same position 3 times in a row? */
	if(m>=8)
	{
		n=m-4;
		o=m-8;
		/* printf("%d: pc%d => %d,%d %d => %d,%d %d => %d,%d\n",m,  his[o].pc,his[o].to[0],his[o].to[1],  his[n].pc,his[n].to[0],his[n].to[1],   his[m].pc,his[m].to[0],his[m].to[1]); */
		
		if((his[o].pc == his[n].pc) && (his[o].to[0] == his[n].to[0]) && (his[o].to[1] == his[n].to[1]) && (his[m].pc == his[n].pc) && (his[m].to[0] == his[n].to[0]) && (his[m].to[1] == his[n].to[1]))
			return 2;
	} 
	
	/* Have fifty moves occurred without a take or a pawn move? */
	if(m>=50)
	{
		for(n=m;n>=(m-50);n--)
		{
			if((his[n].act == 'T') || (his[n].thispc[0] == 'P')); return 0;
		}
	}
	else return 0;
	
	return 1;
}

int make_potential_moves( void )
{
    int tofile,torank,fromfile,fromrank,to,from,relrank,relfile,self,target,rank,file,r,f;
    int krank,kfile,krank2,kfile2,prank,pfile,k,p,princ,pfinc,pinned,cnt;
    char temp[10][10][1];
    char mark;  
    
    /* map relative moves onto potential moves matrix */
    for(fromrank=1;fromrank<=8;fromrank++)
    {
      for(fromfile=1;fromfile<=8;fromfile++)
      { 
            from = board[fromrank][fromfile];
        if((from > -1) && (from < 48))
        {
          for(torank=1;torank<=8;torank++)
          {
            for(tofile=1;tofile<=8;tofile++)
            {
            to = board[torank][tofile];
            relrank = (7-fromrank)+torank;
            relfile = (7-fromfile)+tofile;
            if((to == -1) && (pc[from].relmov[relrank][relfile][0] == 'T')) 
            {   /* vacant square */
                if(pc[from].lname[0] == 'P') /* this is a pawn */
                { 
                    
                    if((pc[from].color == 'W') && (fromrank == 5) && (his[hisnum-1].thispc[0] == 'P'))
                    {	/* enpassant white */
                    	/* came from original rank - same rank - files are adjacent*/
                    	if((his[hisnum-1].from[0] == 7) && (his[hisnum-1].to[0] == 5) && (tofile == his[hisnum-1].to[1]))
                    	{
                    		pc[from].potmov[torank][tofile][0] = 'X';
                    		enpass[0] = fromrank;
                    		enpass[1] = fromfile;
                    		enpass[2] = torank;
                    		enpass[3] = tofile;
                    		/* printf("Enpassant: at %d,%d\n",torank,tofile);
                    		exit(0); */
                    	}
                    	else
                    		pc[from].potmov[torank][tofile][0] = 'Y';
                    }
                    else if((pc[from].color == 'B') && (fromrank == 4) && (his[hisnum-1].thispc[0] == 'P'))
                    {	/* enpassant black */
                    	if((his[hisnum-1].from[0] == 2) && (his[hisnum-1].to[0] == 4) && (tofile == his[hisnum-1].to[1]))
                    	{
                    		pc[from].potmov[torank][tofile][0] = 'X';
                    		enpass[0] = fromrank;
                    		enpass[1] = fromfile;
                    		enpass[2] = torank;
                    		enpass[3] = tofile;
                    		/* printf("Enpassant: at %d,%d\n",torank,tofile);
                    		exit(0); */
                    	}
                    	else
                    		pc[from].potmov[torank][tofile][0] = 'Y';
                    }
                    else
                    	pc[from].potmov[torank][tofile][0] = 'Y'; /* Y is for KINGS knowledge of ability to take if moved there */
                }
                else    
                {   pc[from].potmov[torank][tofile][0] = 'X'; } 
    

            }
            else if((to > -1) && (pc[from].relmov[relrank][relfile][0] == 'V'))
            {
            	pc[from].potmov[torank][tofile][0] = 'N';
            }
            else if(pc[from].relmov[relrank][relfile][0] == 'V')
            { /* pawn can move to vacant square */
            	pc[from].potmov[torank][tofile][0] = 'V';
            }
            else if((pc[from].relmov[relrank][relfile][0] == 'T') && (pc[from].color == pc[to].color)) /* own occ */  
            {
                if((pc[from].lname[0] == 'P') || (pc[from].lname[2] == 'N') || (pc[from].lname[1] == 'N'))
                {   /* PAWN, KING or KNIGHT*/
                    pc[from].potmov[torank][tofile][0] = 'O'; } /* WAS N */
                else    
                {   pc[from].potmov[torank][tofile][0] = 'O'; } /* O is for owner */
            }
            else if((pc[from].relmov[relrank][relfile][0] == 'T') && (pc[from].color != pc[to].color)) /* opp occ - K is for kill */
            {
                pc[from].potmov[torank][tofile][0] = 'K';
            }
            else if(pc[from].relmov[relrank][relfile][0] == 'C') /* C is for castle */
            {
                pc[from].potmov[torank][tofile][0] = 'C';
            }
            else if(to == from) /* self - P is for piece*/
            {
                pc[from].potmov[torank][tofile][0] = 'P';
            }
            else
            {   
                pc[from].potmov[torank][tofile][0] = 'N';
            }
          }
          }
        }
      }
    
    }
    
    /* narrow down potential moves to disclude passing through other pieces. Mark oppenents as K for kill beyond which
       mark vacant spaces and next opponents piece with S for shadow. This will be used to find "pinned" pieces.*/
    for(fromrank=1;fromrank<=8;fromrank++)
    {
      for(fromfile=1;fromfile<=8;fromfile++)
      {
        self = board[fromrank][fromfile];   
        if((self > -1) && (self < 48))
        {
            if((pc[self].lname[0] == 'P') && (pc[self].color == 'W')) 
            {   /* white pawn */
                if((pc[self].potmov[fromrank+1][fromfile][0] == 'N') && (fromrank < 8)) pc[self].potmov[fromrank+2][fromfile][0] = 'N';
            }
            else if((pc[self].lname[0] == 'P') && (pc[self].color == 'B')) 
            {   /* black pawn */
                if((pc[self].potmov[fromrank-1][fromfile][0] == 'N') && (fromrank < 8)) pc[self].potmov[fromrank-2][fromfile][0] = 'N';
            }
            else if((pc[self].lname[0] == 'R') || (pc[self].lname[0] == 'B') || (pc[self].lname[0] == 'Q'))
            {   /* ROOK, BISHOP or QUEEN */ 
                if(pc[self].lname[0] != 'B')
                {   /* ROOK or QUEEN */
                    mark = 'X';
                                        for(torank=fromrank-1;torank>=1;torank--)
                                        {       /* Fly south for the winter! */
                                                /* Xs adjacent to piece are skipped PXXXXKSSSSNNNNN*/
                                                if(mark == 'N')
                                                {       pc[self].potmov[torank][fromfile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][fromfile][0] == 'X'))
                                                {       pc[self].potmov[torank][fromfile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][fromfile][0] == 'K'))
                                                {       
                                                		mark = 'N';
                                                        pc[self].potmov[torank][fromfile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[torank][fromfile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[torank][fromfile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[torank][fromfile][0] == 'K')
                                                {       
                                                		mark = 'S';
                                                        pc[self].potmov[torank][fromfile][0] = 'K';  
                                                }
                                        }
                                        mark = 'X'; 
                                        for(torank=fromrank+1;torank<=8;torank++)
                                        {       /* It gets cooooooold up here in da nortland! */
                                                if(mark == 'N')
                                                {       pc[self].potmov[torank][fromfile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][fromfile][0] == 'X'))
                                                {       pc[self].potmov[torank][fromfile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][fromfile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[torank][fromfile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[torank][fromfile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[torank][fromfile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[torank][fromfile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[torank][fromfile][0] = 'K';  
                                                }
                                        }
                                        mark = 'X';
                                        for(tofile=fromfile-1;tofile>=1;tofile--)
                                        {       /* Go west! */
                                                if(mark == 'N')
                                                {       pc[self].potmov[fromrank][tofile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[fromrank][tofile][0] == 'X'))
                                                {       pc[self].potmov[fromrank][tofile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[fromrank][tofile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[fromrank][tofile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[fromrank][tofile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[fromrank][tofile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[fromrank][tofile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[fromrank][tofile][0] = 'K';  
                                                }
                                        }
                                        mark = 'X';
                                        for(tofile=fromfile+1;tofile<=8;tofile++)
                                        {       /* Out east, they pok cos in the hovod yod */        
                                                if(mark == 'N')
                                                {       pc[self].potmov[fromrank][tofile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[fromrank][tofile][0] == 'X'))
                                                {       pc[self].potmov[fromrank][tofile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[fromrank][tofile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[fromrank][tofile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[fromrank][tofile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[fromrank][tofile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[fromrank][tofile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[fromrank][tofile][0] = 'K';  
                                                }
                                        }
    
                    
                                }
                                if(pc[self].lname[0] != 'R')
                                {       /* BISHOP or QUEEN */
                                    mark = 'X';
                                    for(torank=fromrank+1,tofile=fromfile+1;((torank<=8) && (tofile<=8));torank++,tofile++) 
                                    {       /* Minneapolis Nordeast is getting artsy fartsy! */
                                                if(mark == 'N')
                                                {       pc[self].potmov[torank][tofile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'X'))
                                                {       pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[torank][tofile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[torank][tofile][0] = 'K';  
                                                }
                                        }
                                        mark = 'X';
                                        for(torank=fromrank+1,tofile=fromfile-1;((torank<=8) && (tofile>=1));torank++,tofile--) 
                                        {       /* Theres a whole lotta NASCAR goin' on down in the Southeast  */
                                                if(mark == 'N')
                                                {       pc[self].potmov[torank][tofile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'X'))
                                                {       pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[torank][tofile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[torank][tofile][0] = 'K';  
                                                }
                                        }
                                        mark = 'X';
                                        for(torank=fromrank-1,tofile=fromfile-1;((torank>=1) && (tofile>=1));torank--,tofile--)
                                        {       /* I love Southwestern Food - Hot & Spicy! */
                                                if(mark == 'N')
                                                {       pc[self].potmov[torank][tofile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'X'))
                                                {       pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[torank][tofile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[torank][tofile][0] = 'K';  
                                                }
                                        }
                                        mark = 'X';
                                        for(torank=fromrank-1,tofile=fromfile+1;((torank>=1) && (tofile<=8));torank--,tofile++) 
                                        {   /* Vast tracks of rugged land in the Northwest */
                                                if(mark == 'N')
                                                {       pc[self].potmov[torank][tofile][0] = 'N'; 
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'X'))
                                                {       pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if((mark == 'S') && (pc[self].potmov[torank][tofile][0] == 'K'))
                                                {       mark = 'N';
                                                        pc[self].potmov[torank][tofile][0] = 'S';
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'O')
                                                {       mark = 'N'; 
                                                        pc[self].potmov[torank][tofile][0] = 'O';   
                                                }
                                                else if(pc[self].potmov[torank][tofile][0] == 'K')
                                                {       mark = 'S';
                                                        pc[self].potmov[torank][tofile][0] = 'K';  
                                                }
                                        }

                        }
                }
        }   
      }
    }
    /* KINGS */
        /* I need to do loop once for each king. White kings number is 4 while black
       kings number is 28. This, although strange, does the trick. */

    for(k=4;k<29;k+=24) 
    {
        for(rank=1;rank<=8;rank++)
        {
            for(file=1;file<=8;file++)
            {
                if((pc[k].potmov[rank][file][0] == 'X') || (pc[k].potmov[rank][file][0] == 'K')) /* so far, we think he can move here */
                {
                   for(from=0;from<48;from++)
                   {
                        if((pc[from].color != pc[k].color) && ((pc[from].potmov[rank][file][0] == 'O') || (pc[from].potmov[rank][file][0] == 'X')  || (pc[from].potmov[rank][file][0] == 'Y')) )
                        {   /* opponents pieces only - can't take piece if marked O because it is protected - can't move to X - can't move into PAWN's "take spots" */
                               pc[k].potmov[rank][file][0] = 'N';
                        }
                   }
                }
            }
        }
    }
    
    /* find pinned pieces and remake their potential moves to make it impossible for player to move into
       check by moving a pinned piece out of "check path" */

    for(k=4;k<29;k+=24) 
    {
        for(rank=1;rank<=8;rank++)
        {
            for(file=1;file<=8;file++)
            {
                if(board[rank][file] == k) /* we have found a king */
                {
                    krank = rank;
                    kfile = file;
                }
            }
        }
        /* printf("kings rf: %d,%d\n",krank,kfile); */
        /* now go through board looking for potential moves of pieces where kings position is S (in shadow) */
        for(rank=1;rank<=8;rank++)
        {
            for(file=1;file<=8;file++)
            {
                p = board[rank][file];
                if((p > -1) && (p < 48) && (pc[p].color != pc[k].color) && (pc[p].potmov[krank][kfile][0] == 'S')) 
                {   /* we have an opponent piece pinning a piece */
                
                    /* printf("pinning rf: %d => %d,%d\n",p,rank,file); */
                    /* create a temporary potmov array to check against pinned piece's potmov when found */
                    for(prank=0;prank<=10;prank++)
                    {
                        for(pfile=0;pfile<=10;pfile++)
                        {
                            temp[prank][pfile][0] = 'N';
                        }
                    }
                
                    /* these ifs create values for loop instead of testing all 8 directions separately */
                    if(rank < krank) princ = 1;
                    else if(rank == krank) princ = 0;
                    else princ = -1;
                    
                    if(file < kfile) pfinc = 1;
                    else if(file == kfile) pfinc = 0;
                    else pfinc = -1;
                    
                    for(prank=rank,pfile=file;!((prank==krank) && (pfile==kfile));prank+=princ,pfile+=pfinc)
                    {
                        temp[prank][pfile][0] = 'X'; /* save these */
                        if((board[prank][pfile] > 0) && (board[prank][pfile] < 48) && (board[prank][pfile] != p)) /* we have found the pinned piece */
                        {
                            pinned = board[prank][pfile];
                            /* printf("pinned: %d => %d,%d\n",pinned, prank,pfile); */
                        }
                    }
                    
                    /* remove potential moves from pinned piece that are outside of "pin path" */
                    for(prank=1;prank<=8;prank++)
                    {
                        for(pfile=1;pfile<=8;pfile++)
                        {
                            if((temp[prank][pfile][0] != 'X') && (pc[pinned].potmov[prank][pfile][0] != 'S') && (pc[pinned].potmov[prank][pfile][0] != 'O'))
                            {   /* We have to save the S marks because of double pin possibility - K  Q   Q   K */
                               pc[pinned].potmov[prank][pfile][0] = 'N';
                            }
                        }
                    }
                }
            }
        }
    
    }
    
        
    
    cnt = 0;
    if(whosturn == 'W') k = 4;
    else k = 28;
    
    
    
    /* castling */
    if(k==4) r=1;
    else if(k==28) r=8;
    /* check to see if KING can castle queenside */
    if(pc[k].potmov[r][3][0] == 'C')
    {
    	for(f=1;((f<=5) && (pc[k].potmov[r][3][0]=='C'));f++)
    	{
            if((f!=1) && (f!=5) && (board[r][f] > -1)) pc[k].potmov[r][3][0]='N';
            else if( is_in_threat( r, f, whosturn )) pc[k].potmov[r][3][0]='N';
    	}
    	if(pc[k].potmov[r][3][0]!='N') pc[k].potmov[r][3][0] = 'X'; /* if it's not 'N' then KING can castle */
    }
    
    /* check to see if KING can castle kingside */
    if(pc[k].potmov[r][7][0] == 'C')
    {
    	for(f=5;((f<=8) && (pc[k].potmov[r][7][0]=='C'));f++)
    	{
            if((f!=5) && (f!=8) && (board[r][f] > -1)) pc[k].potmov[r][7][0]='N';
            else if( is_in_threat( r, f, whosturn )) pc[k].potmov[r][7][0]='N';
    	}
    	if(pc[k].potmov[r][7][0]!='N') pc[k].potmov[r][7][0] = 'X'; /* if it's not 'N' then KING can castle */
    }
    
    
    
    
    /* find this king's rank and file */
    kfile = 0;
    for(rank=1;((rank<=8) && (kfile==0));rank++)
    {
        for(file=1;file<=8;file++)
        {
            if(board[rank][file] == k) /* we have found the king */
            {
                krank = rank;
                kfile = file;
            }
        }
    }
    
    if( is_in_check( whosturn ) )
    { /* reduce moves to ones that get player out of check */
        for(p=0;((p<48) && (cnt<2));p++)
        { /* if cnt is 2 or more then player is double checked and can only move king */
        
            if((pc[p].color != pc[k].color) && (pc[p].potmov[krank][kfile][0] == 'K'))
            {   /* opponents pieces only - find pieces with kill on KING */
                cnt++;
                for(rank=1;rank<=8;rank++)
                {
                    for(file=1;file<=8;file++)
                    {
                        if(board[rank][file] == p) /* we have found the piece's rank and file */
                        {
                            prank = rank;
                            pfile = file;
                        }
                    }
                }
                
                if((pc[p].lname[0] == 'P') || (pc[p].lname[1] == 'N'))
                { /* then player can only move KING or take PAWN or take KNIGHT */
                    /* lets take each of players pieces and remove all moves except for
                    ones that take piece except for king */
                    for(to=0;to<48;to++)
                    {   
                        if((pc[to].color == pc[k].color) && (to != k))
                        {   /* color must match players - but don't change king's moves */
                            for(r=1;r<=8;r++)
                            {
                                for(f=1;f<=8;f++)
                                {
                                    if( !((r==prank) && (f==pfile)) && ((pc[to].potmov[r][f][0] == 'K') || (pc[to].potmov[r][f][0] == 'X') || (pc[to].potmov[r][f][0] == 'V'))) 
                                    {   /* don't change potential kill - only change Xs, Ks, Vs */
                                        pc[to].potmov[r][f][0] = 'N';
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    /* QUEEN, ROOK, or BISHOP may be blocked or taken */
                    /* reinitialize temp for valid block and kill */
                    for(r=0;r<=10;r++)
                    {
                        for(f=0;f<=10;f++)
                        {
                            temp[r][f][0] = 'N';
                        }
                    }
                    
                    if(prank < krank) princ = 1;
                    else if(prank == krank) princ = 0;
                    else princ = -1;
                    
                    if(pfile < kfile) pfinc = 1;
                    else if(pfile == kfile) pfinc = 0;
                    else pfinc = -1;
                    
                    for(rank=prank,file=pfile;!((rank==krank) && (file==kfile));rank+=princ,file+=pfinc)
                    {
                        temp[rank][file][0] = 'X'; /* blocks including a kill */
                    }
                    
                    /* remove kings move directly away from check */
                    r=krank+princ;
                    f=kfile+pfinc;
                    pc[k].potmov[r][f][0] = 'N';
                    
                    /* now map temp's Xs onto each players potential moves except for king */
                    for(to=0;to<48;to++)
                    {   
                        if((pc[to].color == pc[k].color) && (to != k))
                        {   /* color must match players - but don't change king's moves */
                            for(r=1;r<=8;r++)
                            {
                                for(f=1;f<=8;f++)
                                {
                                    if((temp[r][f][0] != 'X') && ((pc[to].potmov[r][f][0] == 'X') || (pc[to].potmov[r][f][0] == 'K') || (pc[to].potmov[r][f][0] == 'V')) )
                                    {   /* don't change potential blocks or kill - only change Xs and Ks */
                                        pc[to].potmov[r][f][0] = 'N';
                                    }
                                }
                            }
                        }
                    }
                }
                
            }
        }
    
    }
    
    if(cnt>1) /* double check - player can only move KING to get out of check */
    {
        for(p=0;p<48;p++)
        {
            if((pc[p].color == pc[k].color) && (p != k))
            {   /* opponents pieces only - find pieces with kill on KING */
                for(rank=1;rank<=8;rank++)
                {
                   for(file=1;file<=8;file++)
                   {
                        if((pc[p].potmov[rank][file][0] == 'K') || (pc[p].potmov[rank][file][0] == 'X') || (pc[p].potmov[rank][file][0] == 'V') )
                        {   pc[p].potmov[rank][file][0] = 'N'; } /* we'll leave Os, Ss, and Ps incase we need them */
                   }
                }
            }
        }
    }
    
    /* inhibit KINGS from moving onto eachothers soil */
    for(rank=1;rank<=8;rank++)
    {
    	for(file=1;file<=8;file++)
    	{
    		if(board[rank][file] == 4) /* WHITE KING */
    		{
    			krank=rank;
    			kfile=file;
    		}
    		else if(board[rank][file] == 28) /* BLAK KING */
    		{
    			krank2=rank;
    			kfile2=file;
    		}
    	}
    }
    
    for(r=(krank-1);r<=(krank+1);r++)
    {
    	for(f=(kfile-1);f<=(kfile+1);f++)
    	{
    		pc[28].potmov[r][f][0] = 'N';	
    	}
    }
    for(r=(krank2-1);r<=(krank2+1);r++)
    {
    	for(f=(kfile2-1);f<=(kfile2+1);f++)
    	{
    		pc[4].potmov[r][f][0] = 'N';	
    	}
    }
    
    
    
    return 1;
    
}

int promote_pawn(int newp, int *pto )
{
    int torank = *pto;
    int tofile = *(pto + 1);
	int pawn = board[torank][tofile];
	int r,f;
	
	board[torank][tofile] = newp; /* replace pawn with new piece */
	
		/* erase pot and rel moves for this piece */
		for(r=0;r<10;r++) 
        {
            for(f=0;f<10;f++) pc[pawn].potmov[r][f][0] = 'N';
        }
        
        for(r=0;r<15;r++) 
        {
            for(f=0;f<15;f++) pc[pawn].relmov[r][f][0] = 'N';
        }
	
	
	return 1;
}
    
    
int move_piece(int *pfrom, int *pto )
{
    int torank = *pto;
    int tofile = *(pto + 1);
    int fromrank = *pfrom;
    int fromfile = *(pfrom + 1);
    int from,to,p,r,f,i;
    
    /* check if good information and legal move */
    /* return 0: bad information / no piece etc. */
    /* return 1: legal move */
    /* return 2: pawn promotion */
    /* return 102: wrong turn */
    /* return 103: illegal move */
    
    if((torank < 1) || (torank > 8) || (tofile < 1) || (torank > 8)) return 0;
    else if((fromrank < 1) || (fromrank > 8) || (fromfile < 1) || (fromfile > 8)) return 0;
    else if((board[torank][tofile] < -1) || (board[torank][tofile] > 47)) return 0;
    else if((board[fromrank][fromfile] < 0) || (board[fromrank][fromfile] > 47)) return 0;

    from = board[fromrank][fromfile];
    to = board[torank][tofile]; 
    if(pc[from].color != whosturn) return 102;
    else if(pc[from].potmov[torank][tofile][0] == 'N') return 103; /* basic illegal move */
    else if(pc[to].color == whosturn) return 103; /* player tried to take their own piece */
    else if((pc[from].potmov[torank][tofile][0] == 'V') && (to > -1)) return 103;  /* this move cannot take a piece
                                                     and square is not vacant ( pawn forward )*/ 
    else if(pc[from].potmov[torank][tofile][0] == 'Y') return 103;
    
    strcpy(his[hisnum].thispc,pc[from].lname);
	
    /* Remove piece if not vacant and record in history */
    p = board[torank][tofile];
    if(p > -1)
    {
    	his[hisnum].act = 'T'; /* takes */
    	
    	/* if a rook was taken, remove castle move on that side */
    	if(p == 0) pc[4].relmov[7][5][0] = 'N';
    	else if(p == 7) pc[4].relmov[7][9][0] = 'N';
    	else if(p == 24) pc[28].relmov[7][5][0] = 'N';
		else if(p == 31) pc[28].relmov[7][9][0] = 'N';
    }
    else if((enpass[0] == fromrank) && (enpass[1] == fromfile) && (enpass[2] == torank) && (enpass[3] == tofile))
    { /* enpassant */
    	his[hisnum].act = 'E';
    	p = board[fromrank][tofile]; /* the piece taken had the same rank before move and same file after move */
    	board[fromrank][tofile] = -1;
    }
    else
    {
    	his[hisnum].act = 'V'; /* takes */
    	strcpy(his[hisnum].takespc,"VACANT");
    }
    
    if((his[hisnum].act == 'T') || (his[hisnum].act == 'E'))
    {
    	strcpy(his[hisnum].takespc,pc[p].lname);
        for(r=0;r<10;r++) 
        {
            for(f=0;f<10;f++) pc[p].potmov[r][f][0] = 'N';
        }
        
        for(r=0;r<15;r++) 
        {
            for(f=0;f<15;f++) pc[p].relmov[r][f][0] = 'N';
        }
    }
    for(i=0;i<4;i++) enpass[i] = 0; /* reset */
    
    /* record rest of history */
    his[hisnum].pc = board[fromrank][fromfile];
    his[hisnum].from[0] = fromrank;
    his[hisnum].from[1] = fromfile;
    his[hisnum].to[0] = torank;
    his[hisnum].to[1] = tofile;
    
    
    /* Move piece*/
    board[torank][tofile] = board[fromrank][fromfile];
    board[fromrank][fromfile] = -1;
    
    /* assign new temp from and to */
    from = board[fromrank][fromfile];
    to = board[torank][tofile];
    
    
    
    if(pc[to].lname[0] == 'P') /* is this a PAWN? */
    {
    	if((torank == 1) || (torank == 8)) 
    	{
    		if(his[hisnum].act == 'T')
    			his[hisnum].act = 'Q';
    		else
    			his[hisnum].act = 'P';
    		return 2; /* promote pawn */
    	}
        if(pc[to].color == 'W')
        {       /* pawn has moved so it can longer move two spaces */ 
            if(pc[to].relmov[9][7][0] != 'N') pc[to].relmov[9][7][0] = 'N';
        }
        else if(pc[to].color == 'B')
        {   /* pawn has moved so it can longer move two spaces */
            if(pc[to].relmov[5][7][0] != 'N') pc[to].relmov[5][7][0] = 'N';
        }
    }
    else if(pc[to].lname[2] == 'N') /* is this a KING? */
    {	/* remove castle relmoves */
        pc[to].relmov[7][5][0] = 'N';
        pc[to].relmov[7][9][0] = 'N';
        
        /* finish castling */
        if((to==4) && (fromrank==1) && (torank==1) && (fromfile==5) && (tofile==3))
        {
        	his[hisnum].act = 'C';
        	board[1][4] = 0;
        	board[1][1] = -1;
        }
        else if((to==4) && (fromrank==1) && (torank==1) && (fromfile==5) && (tofile==7))
        {
        	his[hisnum].act = 'C';
        	board[1][6] = 7;
        	board[1][8] = -1;
        }
        else if((to==28) && (fromrank==8) && (torank==8) && (fromfile==5) && (tofile==3))
        {
        	his[hisnum].act = 'C';
        	board[8][4] = 24;
        	board[8][1] = -1;
        }
        else if((to==28) && (fromrank==8) && (torank==8) && (fromfile==5) && (tofile==7))
        {
        	his[hisnum].act = 'C';
        	board[8][6] = 31;
        	board[8][8] = -1;
        }
        
    }
    else if(pc[to].lname[0] == 'R') /* is the a ROOK? */
    {	/* remove castle move on that side */
    	if(to == 0) pc[4].relmov[7][5][0] = 'N';
    	else if(to == 7) pc[4].relmov[7][9][0] = 'N';
    	else if(to == 24) pc[28].relmov[7][5][0] = 'N';
		else if(to == 31) pc[28].relmov[7][9][0] = 'N'; 
    }
    
    hisnum++;
    return 1;
}

int display_board( char perspective, int showpotmovesfor )
{
    /* general variables */
    int i,j,k,istart,iinc,icount,jstart,jinc,jcount;
    if( perspective == 'W')
    {
        istart = 8;
        iinc = -1;
        jstart = 1;
        jinc = 1; 
    }
    else
    {
        istart = 1;
        iinc = 1;
        jstart = 8;
        jinc = -1;
    }

    printf("    ______________________________________________________________________________________\n"); 
    printf("    |                                                                                     |\n");
    printf("    |  _________________________________________________________________________________  |\n");
    for(i=istart,icount=0;icount<8;i+=iinc,icount++)
    {
        printf("    |  ");  
        for(j=jstart,jcount=0;jcount<8;j+=jinc,jcount++)
        {
        	if( (showpotmovesfor >= 0) && (pc[showpotmovesfor].potmov[i][j][0] != 'N'))
               printf("| ** %c ** ",pc[showpotmovesfor].potmov[i][j][0]);
            else if((i == his[hisnum-1].to[0]) && (j == his[hisnum-1].to[1]))
            	printf("||||||||||");
            else
               	printf("|         ");
        }
        printf("|  |\n    |  ");
        for(j=jstart,jcount=0;jcount<8;j+=jinc,jcount++) 
        {
        	k = board[i][j];
            if(k>=0)
            {
                if(pc[k].color == 'W') printf("|  WHITE  ");
                else printf("|  BLACK  ");  
            }                              
            else printf("|         ");
        }
        printf("|%d |\n    | %d",i,i);  
        for(j=jstart,jcount=0;jcount<8;j+=jinc,jcount++)    
        {
        	k = board[i][j];
        	if(k>=0) printf("|%7s  ",pc[k].lname);
        	else printf("|         ");
        }
        printf("|  |\n    |  ");    
        for(j=jstart,jcount=0;jcount<8;j+=jinc,jcount++)    
        {
        	if((i == his[hisnum-1].to[0]) && (j == his[hisnum-1].to[1]))
            	 printf("||||||||||");
            else printf("|_________");     
        }
        printf("|  |\n");   
        }
    printf("    |  ");
    for(j=jstart,jcount=0;jcount<8;j+=jinc,jcount++)
        {
                printf("     %c    ",(char *)(j+64));
        }
    printf("   |\n");
    printf("    |_____________________________________________________________________________________|\n\n\n");
    return 1;
}   

int new_piece( char ch, char color )
{
	int i=47,j,k,r,f;
	while(pc[i].color != 'U') i--; /* new piece number will be p */
	
				pc[i].color = color;
				
                if(ch == 'R')
                {   /* ROOK */
                    strcpy(pc[i].lname,"ROOK");
                    pc[i].mob = 14.00;
                    for(r=0;r<=14;r++)
                    {
                        for(f=0;f<=14;f++)
                        {
                            if(((r==7) || (f==7)) && !((r==7)&&(f==7))) pc[i].relmov[r][f][0] = 'T';
                        }
                    }   
            
                }
                else if(ch == 'K')
                {   /* KNIGHT */
                    strcpy(pc[i].lname,"KNIGHT");
            		pc[i].mob = 5.25;
            		
                    for(r=5;r<=9;r++)
                    {
                        for(f=5;f<=9;f++)
                        {
                            if((r!=f) && ((r+f) != 14) && (r!=7) && (f!=7))
                            {   pc[i].relmov[r][f][0] = 'T'; }
                        }
                    }
                }
                else if(ch == 'B')
                {   /* BISHOP */
                    strcpy(pc[i].lname,"BISHOP");
                    pc[i].mob = 8.75;
                    
                    for(r=0;r<=14;r++)
                        {
                                for(f=0;f<=14;f++)
                                {
                                    if(((r==f) || ((r+f)==14)) && !((r==7)&&(f==7))) pc[i].relmov[r][f][0] = 'T';
                                }
                        }

                }
                else if(ch == 'Q')
                {   /* QUEEN */
                    strcpy(pc[i].lname,"QUEEN");
                	pc[i].mob = 22.75;
                	
                    for(r=0;r<=14;r++)
                    {
                        for(f=0;f<=14;f++)
                        {
                            if((((r==7) || (f==7)) || ((r==f) || ((r+f)==14))) && !((r==7)&&(f==7)))
                            {    pc[i].relmov[r][f][0] = 'T'; }
                        }
                    }
                }
                else return -1;
	return i;
}

int initialize( void )
{
        int i,j,k,r,f; /* (r)and (f)ile */
        /* initialize history */
    	hisnum=0;
    	whosmove=1;
    	whosturn='W';
    	
    	for(i=0;i<1000;i++)
    	{
    		his[i].from[0] = 0;
    		his[i].from[1] = 0;
    		his[i].to[0] = 0;
    		his[i].to[1] = 0;
    		his[i].act = 'U';
    		his[i].pc = -1;
    		strcpy(his[i].thispc,"U");
    		strcpy(his[i].takespc,"U");
    	}
        
        /* initialize board */
        int newboard[10][10] = { 
            50,50,50,50,50,50,50,50,50,50,
            50, 0, 1, 2, 3, 4, 5, 6, 7,50,
            50, 8, 9,10,11,12,13,14,15,50,
            50,-1,-1,-1,-1,-1,-1,-1,-1,50,
            50,-1,-1,-1,-1,-1,-1,-1,-1,50,
            50,-1,-1,-1,-1,-1,-1,-1,-1,50,
            50,-1,-1,-1,-1,-1,-1,-1,-1,50,
            50,16,17,18,19,20,21,22,23,50,
            50,24,25,26,27,28,29,30,31,50,
            50,50,50,50,50,50,50,50,50,50
           };
           

        for(i=0;i<4;i++) enpass[i] = 0;
        
        for(i=0;i<=10;i++)
        {
           for(j=0;j<=10;j++)
           {
                board[i][j] = newboard[i][j];  
           }
        }

                    /* REFERENCE FOR  RELATIVE MOVES ( relmov )       

                        14 X                    X                    X
                        13    X                 X                 X
                        12       X              X              X
                        11          X           X           X
                        10             X        X        X
                         9                X  K  X  K  X
                         8                K  X  X  X  K
                         7 X  X  X  X  X  X  X  O  X  X  X  X  X  X  X   
                         6                K  X  X  X  K
                         5                X  K  X  K  X
                         4             X        X        X
                         3          X           X           X
                         2       X              X              X
                         1    X                 X                 X
                         0 X                    X                    X
                           0  1  2  3  4  5  6  7  8  9  10 11 12 13 14
                        
            */

        /* give default values to pieces */
        
    
            for(i=0;i<48;i++)
            {   
                /* initialize values */
                strcpy(pc[i].sname,"U"); /* undefined */
                for(j=0;j<10;j++)
                {
                        for(k=0;k<10;k++)
                        {       /* set all potential moves to 'N' for 'NO' */
                                pc[i].potmov[j][k][0] = 'N';
                        }
                }
                for(j=0;j<15;j++)
                {
                        for(k=0;k<15;k++)
                        {       /* set all relative moves to 'N' for 'NO' */
                                pc[i].relmov[j][k][0] = 'N';
                        }
                }
                pc[i].relmov[7][7][0] = 'O';    
                if(i<16) pc[i].color = 'W';
                else if(i<32) pc[i].color = 'B';
                else pc[i].color = 'U'; /* UNDEFINED */ 

                if((i > 7) && (i < 24))
                {   /* PAWNS */
                    strcpy(pc[i].lname,"PAWN");
                    pc[i].mob = 2.00;
                    if(i<16)
                    {    /*WHITE PAWNS*/
                        pc[i].relmov[8][7][0] = 'V';
                        pc[i].relmov[9][7][0] = 'V';
                        pc[i].relmov[8][6][0] = 'T';
                        pc[i].relmov[8][8][0] = 'T';
                    }
                    else
                    {   /*BLACK PAWNS*/
                                pc[i].relmov[6][7][0] = 'V';
                                pc[i].relmov[5][7][0] = 'V';
                                pc[i].relmov[6][6][0] = 'T';
                                pc[i].relmov[6][8][0] = 'T';
                    }
                }
                else if((i==0) || (i==7) || (i==24) || (i==31))
                {   /* ROOKS */
                    strcpy(pc[i].lname,"ROOK");
                    pc[i].mob = 14.00;
                    for(r=0;r<=14;r++)
                    {
                        for(f=0;f<=14;f++)
                        {
                            if(((r==7) || (f==7)) && !((r==7)&&(f==7))) pc[i].relmov[r][f][0] = 'T';
                        }
                    }   
            
                }
                else if((i==1) || (i==6) || (i==25) || (i==30))
                {   /* KNIGHTS */
                    strcpy(pc[i].lname,"KNIGHT");
            		pc[i].mob = 5.25;
            		
                    for(r=5;r<=9;r++)
                    {
                        for(f=5;f<=9;f++)
                        {
                            if((r!=f) && ((r+f) != 14) && (r!=7) && (f!=7))
                            {   pc[i].relmov[r][f][0] = 'T'; }
                        }
                    }
                }
                else if((i==2) || (i==5) || (i==26) || (i==29))
                {   /* BISHOPS */
                    strcpy(pc[i].lname,"BISHOP");
                    pc[i].mob = 8.75;
                    
                    for(r=0;r<=14;r++)
                        {
                                for(f=0;f<=14;f++)
                                {
                                    if(((r==f) || ((r+f)==14)) && !((r==7)&&(f==7))) pc[i].relmov[r][f][0] = 'T';
                                }
                        }

                }
                else if((i==3) || (i==27))
                {   /* QUEENS */
                    strcpy(pc[i].lname,"QUEEN");
                	pc[i].mob = 22.75;
                    for(r=0;r<=14;r++)
                    {
                        for(f=0;f<=14;f++)
                        {
                            if((((r==7) || (f==7)) || ((r==f) || ((r+f)==14))) && !((r==7)&&(f==7)))
                            {    pc[i].relmov[r][f][0] = 'T'; }
                        }
                    }
                }
                else if((i==4) || (i==28))
                {   /* KINGS */
                    strcpy(pc[i].lname,"KING");
                    pc[i].mob = 6.56;
                    
                    for(r=6;r<=8;r++)
                    {
                        for(f=6;f<=8;f++)
                        { 
                            if(!((r==7) && (f==7)))
                            {   pc[i].relmov[r][f][0] = 'T'; }
                        }
                    }
                    pc[i].relmov[7][5][0] = 'C'; /* castle queenside*/
                    pc[i].relmov[7][9][0] = 'C'; /* castle kingside*/
                }
                else
                {
                	strcpy(pc[i].lname,"U"); /* undefined */
                }
            }
    return 1;

}

int random_number( int limit )
{   /* get random number between and including 0 and limit
    must include <stdlib.h> and <time.h>                */
    int n,stime;
    long ltime;
    double r,m,rm;
    
    rm = RAND_MAX;
    ltime = time(NULL);
    stime = (unsigned) ltime/2;
    srand(stime);
    m = rand();
    n = (m / rm) * limit;
    return n;
}

