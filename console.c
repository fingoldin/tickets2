#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>

#define LENGTH  1000

long fsize(FILE * fp)
{
	fseek(fp, 0, SEEK_END); 
	long size = ftell(fp);
	fseek(fp, 0, SEEK_SET); 

	return size;
}

int main(int argc, char ** argv)
{
	while(1)
	{
		FILE * f = fopen("./logging.txt", "r");

		if(f)
		{
			long s = fsize(f);
			char * buf = malloc(s+1);

			if(s < LENGTH)
				fseek(f, -s, SEEK_END);
			else
				fseek(f, -LENGTH, SEEK_END);

			fread(buf, 1, s, f);
			buf[s] = '\0';

			printf("%s", buf);
			fclose(f);
		}

		usleep(100000);
	}

	return 0;
}
