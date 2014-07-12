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
int Temperature, Humidity;
int count_T, count_H;

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

void checkData(int count) {
	int i;
	radio.write(buf, 32);
	for (i=0; i<20; i++);
	radio.startListening();
	count_T = 0;
	count_H = 0;
	Temperature = 0;
	Humidity = 0;
	for (i=0; i<10; i++) {
		radio.read(buf, 32);
		if (buf[2] == 49 && buf[3] == 49) {
			printf("Garbage Message, Drop it!\n");
		} else if (buf[2] == 0 && buf[3] == 0){
			printf("Garbage Message, Drop it!\n");
		} else {
			if (count_T == 0) {
				Temperature = buf[2];
				count_T++;
			} else {
				if (Temperature == buf[2]) count_T++;
			}
			if (count_H == 0) {
				Humidity = buf[3];
				count_H++;
			} else {
				if (Humidity == buf[3]) count_H++;
			}
		}
	}
	count++;
	radio.stopListening();

	if (count_H < 3 || count_T < 3) {
		for (i=0; i<20; i++);
		if (count < 5) checkData(count);
	}
}

int main( int argc, char** argv){
	int i;
	int count = 0;
	uint64_t Tx_pipe;

	if (argc < 3) {
		printf("Error: unknown message");
		exit(1);
	} else {
		sscanf(argv[1], "%016llx", &Tx_pipe);
		setup(Tx_pipe);

		buf[0] = '1';
		buf[1] = *argv[2];
		radio.stopListening();

		if (argc == 3) {
			checkData(count);
			printf("Temperature: %d, Humidity: %d\n", Temperature, Humidity);
			if (*argv[2] == '1') return Temperature;
			else if (*argv[2] == '2') return Humidity;
			else return 0;
		} else if (argc == 6) {
			buf[4] = atoi(argv[3]);
			buf[5] = atoi(argv[4]);
			buf[7] = *argv[5];
			for (i=0; i<10; i++)
				radio.write(buf, 32);
			printf("Have send the message!\n"); 
			return 0;		
		} else {
			return 0;
		}
	}
}
