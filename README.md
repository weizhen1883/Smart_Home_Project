Smart_Home_Project
==================
  Device
===========
Socket and Switch
 ./Socket_and_SWitch [address] [check/set: check->0; set->1] [on/off: on-> 1; off->0]
 
Temperature and Humidity Sensor
./TH_Sensor [address] [check/set: set->0; check temperature->1; check humidity->2;] [temperature setting] [humidity setting] [cooling/heater: heater->0; cooling->1]

32-bit BUF
===========
buf[0]               buf[1]      buf[2]				  buf[3]		    buf[4]			   buf[5]		   buf[6]      buf[7]     buf[8]
-------------------  ----------  -------------------  ----------------  -----------------  --------------  ----------  ---------  ----------
0 Socket and Switch  0 set       0 off
					 1 check     1 on
-------------------  ----------  -------------------  ----------------  -----------------  --------------  ----------  ---------  ----------
1 Air Condition      0 set       current temperature  current humidity  temperature limit  humidity limit  0 fan auto  0 heater   0 vent off
					 1~3 check																			   1 fan on    1 cooling  1 vent on
-------------------  ----------  -------------------  ----------------  -----------------  --------------  ----------  ---------  ----------


