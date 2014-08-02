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
int TSet, HSet, FanSet, ModelSet, VentSet;
int count_T, count_H;

void setup(uint64_t Tx_pipe){
	//Prepare the radio module
	printf("\nPreparing interface\n");
	radio.begin();
	radio.setRetries( 15, 15);
	radio.setChannel(0x78);
	radio.setPALevel(RF24_PA_MAX);
	radio.setDataRate(RF24_250KBPS);

	radio.openWritingPipe(Tx_pipe);
	radio.openReadingPipe(1,Rx_pipe);
	radio.startListening();
	radio.printDetails();
}

void checkData(int count) {
	int i;
	radio.write(buf, 32);
	radio.startListening();
	count_T = 0;
	count_H = 0;
	Temperature = 0;
	Humidity = 0;
	for (i=0; i<100; i++) {
		radio.read(buf, 32);
		if (buf[0] == '1' && buf[1] == '1') {
			Temperature = buf[2];
			Humidity = buf[3];
			TSet = buf[4];
			HSet = buf[5];
			FanSet = buf[6];
			ModelSet = buf[7];
			VentSet = buf[8];
			break;
		}
		printf("Temperature: %d, Humidity: %d, TSet: %d, HSet: %d, FanSet: %c; ModelSet: %c, VentSet: %d\n", buf[2], buf[3], buf[4], buf[5], buf[6], buf[7], buf[8]);
	}
	
	radio.stopListening();
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
			printf("Temperature: %d, Humidity: %d, TSet: %d, HSet: %d, FanSet: %c; ModelSet: %c, VentSet: %d\n", Temperature, Humidity, TSet, HSet, FanSet, ModelSet, VentSet);
			if (*argv[2] == '1') return Temperature;
			else if (*argv[2] == '2') return Humidity;
			else if (*argv[2] == '3') return TSet;
			else if (*argv[2] == '4') return HSet;
			else if (*argv[2] == '5') return FanSet;
			else if (*argv[2] == '6') return ModelSet;
			else if (*argv[2] == '7') return VentSet;
			else return 0;
		} else if (argc == 8) {
			buf[4] = atoi(argv[3]);
			buf[5] = atoi(argv[4]);
			buf[6] = *argv[5];
			buf[7] = *argv[6];
			buf[8] = atoi(argv[7]);
			for (i=0; i<10; i++)
				radio.write(buf, 32);
			printf("Have send the message!\n"); 
			return 0;		
		} else {
			return 0;
		}
	}
}
