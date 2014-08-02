#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <mysql/mysql.h>

#define DATABASE_NAME		"TEST"
#define DATABASE_USERNAME	"root"
#define DATABASE_PASSWORD	"1qaz2wsx"

MYSQL *mysql1;

void mysql_connect (void)
{
    //initialize MYSQL object for connections
	mysql1 = mysql_init(NULL);

    if(mysql1 == NULL)
    {
        fprintf(stderr, "%s\n", mysql_error(mysql1));
        return;
    }

    //Connect to the database
    if(mysql_real_connect(mysql1, "localhost", DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME, 0, NULL, 0) == NULL)
    {
    	fprintf(stderr, "%s\n", mysql_error(mysql1));
    }
    else
    {
        printf("Database connection successful.\n");
    }
}

void mysql_disconnect (void)
{
    mysql_close(mysql1);
    printf( "Disconnected from database.\n");
}

void mysql_write_something (void)
{
   //vector times;   //a vector of alarm times

    if(mysql1 != NULL)
    {
        //Retrieve all data from alarm_times
        if (mysql_query(mysql1, "INSERT INTO settings (   \
					id,   \
					value_string   \
				) VALUES (   \
					99,   \
					'Hello'   \
				) \
				ON DUPLICATE KEY UPDATE   \
					id = 99,   \
					value_string = 'Hellow'   \
				"))

        {
            fprintf(stderr, "%s\n", mysql_error(mysql1));
            return;
        }
    }
}

int main(int argc, char const *argv[])
{
	mysql_connect();

   /* if (mysql1 != NULL)
    {
        if (!mysql_query(mysql1, "SELECT value_int, value_string FROM settings WHERE id = 8"))
        {
        	MYSQL_RES *result = mysql_store_result(mysql1);
        	if (result != NULL)
        	{
        		//Get the number of columns
        		int num_rows = mysql_num_rows(result);
        		int num_fields = mysql_num_fields(result);

        		MYSQL_ROW row;			//An array of strings
        		while( (row = mysql_fetch_row(result)) )
        		{
        			if(num_fields >= 2)
        			{
        				char *value_int = row[0];
        				char *value_string = row[1];

        				printf( "Got value %s\n", value_string);
        	        }
        		}
   	            mysql_free_result(result);
        	}
        }

    }*/
    mysql_write_something();

    mysql_disconnect();
	return 0;
}

