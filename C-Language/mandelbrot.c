#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <complex.h>
#include <string.h>

/* NOTE TO SELF: !! YOU MUST CORRECTLY SET IMG_DIR OR IT WILL ERROR !! */

/*********************************** CREDITS ***************************************
*  This mandelbrot program is written in C99 by John Wenzel 12/06/07 to 12/07/07   *
*                                                                                  *
*      MY WEBSITE: http://www.iomass.com    MY EMAIL: johnwenzel@iomass.com        *
*                                                                                  *
*  Feel free to make changes and play with it, but be sure to add new comments for *
*     the changes you have made, and don't remove this comment block. Please send  *
*     me any new versions you may have made. Also let me know of compiling         *
*     successes and failures on your system - please be descriptive here.          *
*  No guarantees of successful and or safe usage are made. I cannot be held        * 
*     responsible for damage to your system. You should be familiar with C and     *
*     the command line if you are going to use this program.                       *
*                                                                                  *
*  It compiled and runs good on my system: Mac OS X 10.4.11, gcc compiled          *
*                                                                                  *
*  PREREQUISITES:                                                                  * 
*     YOU MUST HAVE THE NETPBM ppmtojpeg OR pnmtojpeg INSTALLED ON YOUR SYSTEM     *
*        IN ORDER TO HAVE THE PROGRAM CONVERT THE PPM TO JPEG. IF YOU DON'T HAVE   *
*        THIS, YOU MUST BE ABLE TO VIEW OR CONVERT .ppm FILES SOMEHOW. GOOGLE      *
*        "netpbm" AND YOU WILL FIND NETPBM AND OTHER RESOURCES AT sourceforge.net. *
***********************************************************************************/

/* THESE DEFINITIONS ARE DEFAULTS THAT ARE OVERRIDDEN WHEN VARIABLES ARE PASSED */
#define WIDTH 500 /* pixels or number of samples */ 
#define HEIGHT 500 /* pixels or number of samples */
#define X_START -375 /* ( X_START + inc ) / MAGNIFICATION gives actual decimal */
#define Y_START -250 /* ( Y_START + inc ) / MAGNIFICATION gives actual decimal */
#define MAGNIFICATION 200 /* if MAGNIFICATION = 10, then each pixel's dimensions would be 1/10 x 1/10 */
#define SPECTRUM_COEFFICIENT 50
#define CONVERT_TO_JPEG 0 /* 1 means yes, 0 means no - if 1, PATH_TO_PPMTOJPEG must be set correctly */
#define ITERATION_PERCENTAGE 5       /* see the following comment */
#define ITERATION_THRESHOLD 10         /* must be less than MAX_ITERATIONS - if atleast ITERATION_PERCENTAGE 
                                          of points does not yield this number of iterations, then the 
                                          program exits - this allows you not to not save files that are boring.
                                          The higher the number, the more strict. */

/* THESE DEFINITIONS CAN ONLY BE CHANGED HERE */
#define MAX_ITERATIONS 600 /* maximum times will the mandelbrot equation recurse before exiting */
#define IMG_DIR "img_mandelbrot/" /* where you want images to go */
#define PATH_TO_PPMTOJPEG "/usr/local/netpbm/bin/ppmtojpeg" /* if CONVERT_TO_JPEG = 0, this doesn't matter
                                                               NOTE: netpbm ppmtojpeg and pnmtojpeg are the same,
                                                                     so you can use the other. */

/* GLOBALS */
struct pnt {
   long double complex c;
   int iterations;
} p[ ( WIDTH * HEIGHT ) ];

struct clr {
   unsigned char chan[3];
};

/* PROTOTYPES */
int mandelbrot( long double complex mconstant );
struct clr *get_color( int numerator, int denominator );
int make_ppm_file( char *pfilename, int filenamelen, int width, int height, int spectrum_coefficient ); 
void usage_error( char str[255] );

int main( int argc, char *argv[] ) {
   long double x, y, i;
   int index = 0, j, k, iterations, top_iterations;
   int _x_start, _y_start, _width, _height, _magnification, _spectrum_coefficient;
   int _convert_to_jpeg, _iteration_percentage, _iteration_threshold, num_passed_threshold;
   char filename[100], jpg_filename[100], str[100], *pfilename;
 
   /* GLOBAL DEFAULTS */
   _x_start = X_START;
   _y_start = Y_START;
   _width = WIDTH;
   _height = HEIGHT;
   _magnification = MAGNIFICATION;
   _spectrum_coefficient = SPECTRUM_COEFFICIENT;
   _iteration_percentage = ITERATION_PERCENTAGE;
   _iteration_threshold = ITERATION_THRESHOLD;
   _convert_to_jpeg = CONVERT_TO_JPEG;
   /* OVERRIDE GLOBALS */
   if(argc > 1) {
      /* then variable have been passed */
      for(k=1;k<argc;k+=2) {
         if(strncmp("--help", argv[k], 6 ) == 0) usage_error("Help"); 
         if((k + 2) > argc) usage_error("Wrong option count passed to function");
         for(j=0;j<strlen(argv[k+1]);j++) {
            if((j == 0) && (argv[k+1][0] == '-')) continue; /* allow first character to be negative sign */
            if(isdigit(argv[k+1][j]) == 0) usage_error("Option values can be sent as integers only - no decimals"); 
         }
         if(strncmp("-m", argv[k], 2) == 0) { 
            _magnification = atoi(argv[k+1]);
            if(_magnification < 1) usage_error("Magnification must be atleast 1"); 
         }
         else if(strncmp("-x", argv[k], 2) == 0) {
            _x_start = atoi(argv[k+1]);
         }
         else if(strncmp("-y", argv[k], 2) == 0) {
            _y_start = atoi(argv[k+1]);
         }
         else if(strncmp("-w", argv[k], 2) == 0) {
            _width = atoi(argv[k+1]);
            if(_width < 10) usage_error("Width must be atleast 10");
         }
         else if(strncmp("-h", argv[k], 2) == 0) {
            _height = atoi(argv[k+1]);
            if(_height < 10) usage_error("Height must be atleast 10");
         }
         else if(strncmp("-s", argv[k], 2) == 0) {
            _spectrum_coefficient = atoi(argv[k+1]);
            if(_spectrum_coefficient < 1) usage_error("Spectrum Coefficient must be atleast 1");
         }
         else if(strncmp("-p", argv[k], 2) == 0) {
            _iteration_percentage = atoi(argv[k+1]);
            if((_iteration_percentage < 0) || (_iteration_percentage > 100)) usage_error("Iteration Percentage must be atleast 0 and no greater than 100");
         }
         else if(strncmp("-t", argv[k], 2) == 0) {
            _iteration_threshold = atoi(argv[k+1]);
            if(_iteration_threshold < 0) usage_error("Iteration Threshold must be atleast 0");
         }
         else if(strncmp("-j", argv[k], 2) == 0) {
            if(atoi(argv[k+1]) == 1) _convert_to_jpeg = 1;
            else _convert_to_jpeg = 0;
         }
         else usage_error("Unknown option passed");
      }
      if(_iteration_threshold >= MAX_ITERATIONS) {
         sprintf(str, "Iteration Threshold must be less than MAX_ITERATIONS of %d", MAX_ITERATIONS );
         usage_error( str );
      }
   }

   num_passed_threshold = 0;
   top_iterations = 0;

   /* MAKE FILENAMES */
   sprintf(str, "%s", IMG_DIR );
   strcpy(filename, str);
   sprintf(str, "%d", _magnification);
   strcat(filename, str);
   strcat(filename, "_");
   sprintf(str, "%d", _width );
   strcat(filename, str);
   strcat(filename, "_");
   sprintf(str, "%d", _height );
   strcat(filename, str);
   strcat(filename, "_");
   sprintf(str, "%d", _x_start );
   strcat(filename, str);
   strcat(filename, "_");
   sprintf(str, "%d", _y_start );
   strcat(filename, str);

   strcpy(jpg_filename, filename);
   strcat(filename, ".ppm");
   strcat(jpg_filename, ".jpg");
   pfilename = filename;
   y = _y_start;
   while( y < (_y_start + _height) ) {
      x = _x_start;
      while( x < ( _x_start + _width ) ) {
         p[index].c = ( x / _magnification ) + ( y / _magnification )*I;
         p[index].iterations = mandelbrot( p[index].c );
         if(p[index].iterations <= MAX_ITERATIONS) {
            if(p[index].iterations > top_iterations) top_iterations = p[index].iterations;
            if(p[index].iterations >= _iteration_threshold) num_passed_threshold++;
         }        
         /* printf("%d: %Lf + %Lf -> %d\n", index, creall( p[index].c ), cimagl( p[index].c), p[index].iterations );  */
         x += 1 ;
         index++ ;
      }
      y += 1 ;
   }
   k = floor((num_passed_threshold * 100) / ( _width * _height));
   if(k < _iteration_percentage) {
      printf("No file created. Failed to meet iterations criteria\n");
      printf("Current Criteria: image must have %d%% of points having atleast %d iterations\n", _iteration_percentage, _iteration_threshold);
      printf("This run had only %d%% having atleast %d iterations\n", k, _iteration_threshold);
      printf("Type 'mandelbrot --help' if you don't know how to specify different criteria\n");
   }
   if(make_ppm_file( pfilename, strlen(filename), _width, _height, _spectrum_coefficient )) {
      printf("%s has been created\n", filename);
      printf("This run had %d%% of points having atleast %d iterations\n", k, _iteration_threshold);
      if( _convert_to_jpeg ) {
         sprintf(str,"%s", PATH_TO_PPMTOJPEG );
         strcat(str, " ");
         strcat(str, filename);
         strcat(str, " > ");
         strcat(str, jpg_filename);
         system( str ); 
         sleep( 1 );
         printf("File %s has been created\n", jpg_filename);
         sleep( 1 );
         remove( filename ); 
         strcpy(str, "open ");
         strcat(str, jpg_filename);
         system( str );
      }
   }
   else printf("ERROR: ppm file could not be created\n");
   return 0;
}

int mandelbrot( long double complex mconstant ) {
    long double complex z = 0.0 + 0.0I;
    int iterations = 0;
    while((cabsl( z ) < 2.0) && (iterations++ < MAX_ITERATIONS)) {
      z = (z * z) + mconstant;
   }
   return iterations;
}

struct clr *get_color( int numerator, int denominator )
{
   struct clr color, *pcolor;
   int num_colors = 1530, i, rint = 0, gint = 0, bint = 0;
   double ratio;
   ratio = log(numerator) / log(denominator);
   if(ratio > 1) ratio = 1;
   i = num_colors - floor(ratio * num_colors);
   pcolor = &color;
   if(i <= 255) {
      /* yellow to red */
      rint = 255;
      gint = 255 - i;
      bint = 0;
   }
   else if(i <= 510) {
      /* red to purple */
      rint = 255;
      gint = 0;
      bint = i - 255;
   }
   else if(i <= 765) {
      if(i > 765) i == 765;
      /* purple to blue */
      rint = 765 - i;
      gint = 0;
      bint = 255;
   }
   else if(i <= 1020) {
      /* blue to aqua */
      rint = 0;
      gint = i - 765;
      bint = 255;
   }
   else if(i <= 1275) {
      /* aqua to green */
      rint = 0;
      gint = 255;
      bint = 1275 - i;
   }
   else if(i <= 1530) {
      /* green to black */
      rint = 0;
      gint = 1530 - i;
      bint = 0;
   } 
   pcolor->chan[0] = rint;
   pcolor->chan[1] = gint;
   pcolor->chan[2] = bint;
   return pcolor;
}

int make_ppm_file( char *pfilename, int filenamelen, int width, int height, int spectrum_coefficient ) {
   int i;
   FILE *fp;
   char filename[ filenamelen + 1 ], line[20];
   int x, y, index;
   struct clr color, *pcolor;
   unsigned char nullchar;
   nullchar = 0;
   pcolor = &color;
   for(i=0;i<filenamelen;i++) filename[i] = *(pfilename + i);
   filename[ i ] = '\0';
   if((fp = fopen( filename, "wb")) == NULL) {
      printf("ERROR: Could not create file (%s) to write to\n",filename);
      exit( 0 );
   }
   fputs("P6\n", fp);
   fputs("#PPM Image File generated by C programs written by John Wenzel - johnwenzel@iomass.com\n", fp);
   sprintf(line, "%d %d", width,  height);
   fputs(line,fp);
   fputs("\n255\n",fp);
   fflush(fp);
   y = height - 1;
   while( y >= 0) {
      x = 0;
      while( x < width ) {
         index = ( y * width ) + x;
         if(p[index].iterations > MAX_ITERATIONS) {
            for(i=0;i<3;i++) fputc(nullchar, fp);
         }
         else {
           pcolor = get_color( p[index].iterations, spectrum_coefficient ); 
           for(i=0;i<3;i++) fputc(pcolor->chan[i], fp);         
         }
         x++;
      }
      fflush(fp); 
      y--;
   }
   fclose( fp );
   return 1;
}

void usage_error( char str[255] )
{
   printf("\n_______________________________________________________\n%s\n\n", str);
   printf("TYPE 'mandelbrot --help' TO SEE THIS INFORMATION\n\n");
   printf("USAGE: mandelbrot [-m magnification] [-x left x bound] [-y bottom y bound]\n");
   printf("                  [-w width] [-h height] [-s spectrum coefficient] [-j convert to jpeg]\n");
   printf("                  [-p iteration percentage] [-t iteration threshold]\n\n");
   printf("*If a variable is not passed, it will have the default value in the following list.\n\n");
   printf("OPTION DEFINITIONS AND DEFAULT VALUES:\n");
   printf("   -m %d\n", MAGNIFICATION);
   printf("      magnification is how many pixels per unit (e.g 10 would yield pixels\n      of 1/10 by 1/10)\n");
   printf("   -x %d\n", X_START);
   printf("      x_start / magnification will yield actual decimal of left bound\n");
   printf("   -y %d\n", Y_START);
   printf("      y_start / magnification will yield actual decimal of bottom bound\n");
   printf("   -w %d\n", WIDTH);
   printf("      width of image in pixels - width / magnification yields horizontal span\n");
   printf("   -h %d\n", HEIGHT);
   printf("      height of image in pixels - height / magnification yields vertical span\n");
   printf("   -s %d\n", SPECTRUM_COEFFICIENT);
   printf("      spectrum coefficient stretches and squeezes the spectrum of colors\n      in image - adjust for definition\n");
   printf("   -j %d\n", CONVERT_TO_JPEG );
   printf("      convert to jpeg is 0 for no, 1 for yes - requires netpbm ppmtojpeg\n      function\n");
   printf("   -p %d\n", ITERATION_PERCENTAGE );
   printf("      iteration percentage defines the number of points that must meet\n      'iteration threshold'\n");
   printf("   -t %d\n", ITERATION_THRESHOLD );
   printf("      iteration threshold is the number of iterations that must be met for\n      'iteration percentage' of points\n\n"); 
   printf("THE FOLLOWING OPTIONS MAY BE CHANGED IN mandelbrot.c AND THEN YOU CAN RECOMPILE\n");
   printf("   IMG_DIR (image directory): %s\n", IMG_DIR);
   printf("   CONVERT_TO_JPEG (1 for yes, 0 for no - Requires netpbm ppmtojpeg function): %d\n", CONVERT_TO_JPEG);  
   printf("   PATH_TO_PPMTOJPEG (path to the ppmtojpeg function): %s\n\n", PATH_TO_PPMTOJPEG);
   printf("This program was written by John Wenzel on 12/07/07\n   VISIT: http://www.iomass.com\n   EMAIL ME AT: johnwenzel@iomass.com\n\n");
   exit( 0 );
}
