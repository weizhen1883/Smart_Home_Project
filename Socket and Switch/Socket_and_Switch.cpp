#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <string>
#include <getopt.h>
#include <cstdlib>
#include <iostream>
#include <RF24.h>

using namespace std;
RF24 radio("/dev/spidev0.0",8000000 , 25);  //spi device, speed and CSN,only CSN is NEEDED in RPI
const int role_pin = 7;
const uint64_t Rx_pipe = 0xDEADBEEF01;
uint8_t buf[32];

void setup(uint64_t Tx_pipe){
	//Prepare the radio module
	printf("\nPreparing interface\n");
	radio.begin();
	radio.setRetries( 15, 15);
	radio.setChannel(0x78);
	radio.setPALevel(RF24_PA_MAX);
	radio.setPALevel(RF24_PA_MAX);

	radio.openWritingPipe(Tx_pipe);
	radio.openReadingPipe(1,Rx_pipe);
	radio.startListening();
	radio.printDetails();

}

int main( int argc, char** argv){
	int i;
	int count_1, count_0, status;
	uint64_t Tx_pipe;

	if (argc < 3) {
		printf("Error: unknown message");
		exit(1);
	} else {
		sscanf(argv[1], "%016llx", &Tx_pipe);
		setup(Tx_pipe);

		buf[0] = '0';
		buf[1] = *argv[2];
		radio.stopListening();

		if (argc == 3) {
			radio.write(buf, 32);
			for (i=0; i<10; i++);
			radio.startListening();
			count_1 = 0;
			count_0 = 0;
			for (i=0; i<10; i++) {
				radio.read(buf, 32);
				if (buf[2] == '1') count_1++;
				if (buf[2] == '0') count_0++;
			}
			radio.stopListening();
			if (count_1 > count_0) status = 1;
			else if (count_1 < count_0) status = 0;
			else status = 2;
			printf("\nThe status is %d\n", status);
			return status;
		} else if (argc == 4) {
			buf[2] = *argv[3];
			for (i=0; i<10; i++)
				radio.write(buf, 32);
			printf("Have send the message!\n"); 
			return 0;		
		} else {
			return 0;
		}
	}
}
