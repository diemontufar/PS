#include <stdio.h>
#include <omp.h>			
#include <cstdlib>
#include <iostream>
#include <string>
#include <vector>
#include <sstream>
#include <algorithm>
#include <fstream>

using namespace std;

//Max number of allowed HELIX register lines
const int MAX_LINES = 100;

//files/output_auto4/
//3I5F.pdb

char * buffer;
size_t size_buffer;

// Minimum number of residues in helix (-1)
const int _MIN=9;
// Maximum number of intervals - Seed maximum is 250.0
const int STEPS=46;
// Step-size
const double STEP_SIZE=5.0;
// Initial seed
const double INITIAL_SEED=20.0;
/* Class: 
# 1 = right-handed alpha
# 2 = right-handed omega
# 3 = right-handed pi
# 4 = right-handed gamma
# 5 = right-handed 3-10
# 6 = left-handed alpha
# 7 = left-handed omega 
# 8 = left-handed gamma
# 9 = 2-7 ribbon helix
# 10 = polyproline */
const int _CLASS=1;

int n_lines = 0;
int n_printed = 0;
int n_errors = 0;
double seed = 0.0;
double objmin, minobj = 0.0;
string command = "";
string output_dir = "";
string pdbin = "";

string result_ps = "";
double	wtime	= omp_get_wtime(); // Record the starting time

//Print Statistics 
void printStatistics(double time){
	
	n_errors = n_lines - n_printed;
	string file = output_dir + "statistics.data";

	  ofstream myfile (file.c_str());
	  if (myfile.is_open())
	  {
	    myfile << time;
	    myfile << "\n";
	    myfile << n_lines;
	    myfile << "\n";
	    myfile << n_printed;
	    myfile << "\n";
	    myfile << n_errors;
	    myfile << "\n";
	    myfile.close();
	  }
	  else cout << "Unable to open statistics file";
}

//trim function
string trim(string& str){
    size_t first = str.find_first_not_of(' ');
    size_t last = str.find_last_not_of(' ');
    return str.substr(first, (last-first+1));
}

//String Conversion
string IntToString ( int number ){
  ostringstream oss;
  oss<< number;
  return oss.str();
}

//remove blank spaces function
string removeSpaces(string input)
{
  input.erase(std::remove(input.begin(),input.end(),' '),input.end());
  return input;
}

//Split function
void StringExplode(string str, string separator, vector<string>* results){
    int found;
    found = str.find_first_of(separator);
    while(found != string::npos){
        if(found > 0){
            results->push_back(str.substr(0,found));
        }
        str = str.substr(found+1);
        found = str.find_first_of(separator);
    }
    if(str.length() > 0){
        results->push_back(str);
    }
}

//Execute external program
string GetStdoutFromCommand(string cmd) {

    string data;
    FILE * stream;
    const int max_buffer = 512;
    char buffer_[max_buffer];
    cmd.append(" 2>&1");

    stream = popen(cmd.c_str(), "r");
	    if (stream) {
		    while (!feof(stream))
		    if (fgets(buffer_, max_buffer, stream) != NULL) data.append(buffer_);
	    }
    pclose(stream);
    return data;
}

//Execute automatic PS program
void executeAutomaticProcess(){

	string line = "";
	int index = 0;
	string number, first, last, chain, length, type;

	int myID = omp_get_thread_num();
	int N_Threads = omp_get_num_threads();
	double	wtime = omp_get_wtime();
	string ps_path = "";
	

	//Allocate memory for the resulting array of helices
	string ** helices = new string*[MAX_LINES];
	for(int i=0;i<MAX_LINES;i++){
		helices[i] = new string[7];
	}

	//Assign HELIX registers to a helices array
	for(int i=0; i< size_buffer ; i++) {
		if(buffer[i] != '\n') {
		     
		line = line + buffer[i];

		}else{
			if (!line.find("HELIX")){
				helices[index][0] = "HELIX";//name
				helices[index][1] = line.substr(7, 3);//number
				helices[index][2] = line.substr(21, 5);//first
				helices[index][3] = line.substr(33, 5);//last
				helices[index][4] = line.substr(19, 1);//chain
				helices[index][5] = line.substr(71, 5);//length
				helices[index][6] = line.substr(38, 2);//type 

				n_lines ++;
				index ++;
			}			
			line = ""; //rewind
		}
	}


	if (n_lines < 1){
		cout << "Error. There are not HELIX structures difined on the file." << endl;
		exit(0);
	}	

	//cout << "\nSize: " << size_buffer << endl;
	//cout << "Buffer en 0: " << buffer[0] << endl;
	//cout << "Buffer en size: " << buffer[size_buffer - 2] << endl; 	

	free(buffer);

	//cout << "\nNumber of registers: " << n_lines << '\n';
	//cout << "STARTED;" << endl;

	int j;
	
	/*****************************************Start Automatic Process:*****************************************/
	
	#pragma omp parallel default(shared) shared(N_Threads) private(myID,ps_path,command,result_ps) reduction(+:seed,n_printed)
	{
	#pragma omp master
	{
		N_Threads = omp_get_num_threads();
	}
	myID = omp_get_thread_num();

	#pragma omp for
	for (j=0; j<n_lines;j++){
		
		seed = INITIAL_SEED;

		string lgth = trim(helices[j][5]);
		string typ = trim(helices[j][6]);
		
		if( atoi(lgth.c_str()) > _MIN && atoi(typ.c_str()) == _CLASS  ){
			// Initial objective minimum
			objmin = 99999.0;

				
			for (int k = 1; k < STEPS; k++){

			stringstream s_seed;
			s_seed << seed; 
			
			//Construct automatic Command:
			//ps_path =  IntToString (myID) + "/PS -i ";
			ps_path = "./PS -i ";
			command = ps_path + pdbin  + " -f " + trim(helices[j][2]) + " -l " 
					+ trim(helices[j][3]) + " -c " + trim(helices[j][4]) 
					+ " -a CA -s " + s_seed.str()+ ".0" + " -P auto";
			/*
			#pragma omp critical
    			{
				cout << "\n**************************************";	
				cout << "\nBEGIN...";
			}	
			*/
			result_ps = GetStdoutFromCommand(command);

			//Remove blank spaces:
			result_ps = removeSpaces(result_ps);

			//If result is empty go to the next iteration
			if (result_ps.empty()){
				continue;
			}			

			//split the string
   			vector<string> result_values;
			StringExplode(result_ps, ";", &result_values);

			//If result has values then continue the process
			if (result_values[0] == "OK") {

				//Assign and Update values to the corresponding variables:	
				minobj = atof(result_values[1].c_str()); //minimized objective

				if(minobj < objmin) {
					objmin = minobj;
					//radius =  result_values[2];   //Sphere radius
					//distance = result_values[3];  //Average distance
					//sddist =  result_values[4];   //Average sddistance
					//arclength = result_values[5];  //Helix length
					//cout << "\nGetting values...\n";
			  	}
				seed = seed + STEP_SIZE;
			}else{
				#pragma omp critical
				{
				cout << "Error processing HELIX structure\n";
				}
			}
/*
			#pragma omp critical
			{
			cout << "Command: " << command << endl;
			cout << "Minimized Objective: " << result_values[1].c_str() << endl;
			cout << "Result: " << result_ps << "; Inner for: " << k << "; Outer for: " << j << ";Thread No: " << myID << "; N_threads: " << N_Threads << endl;
			}
*/
			if(objmin == 99999.0) {
			  cout << "Unable to determine helix parameters\n";
			}
			else {

				//cout << "$pdbin $number $radius $distance +/- $sddist $arclength $length\n";
				if (k == STEPS - 1){
				
				string cur_path_file = output_dir + trim(helices[j][1]) + ".out"; 	
				//cout << "Path to file: " << cur_path_file << endl;

				stringstream s_seed;
				s_seed << seed; 
				
				//ps_path =  IntToString (myID) + "/PS -i ";
				ps_path = "./PS -i ";

				command = ps_path + pdbin  + " -f " + trim(helices[j][2]) + " -l " 
					+ trim(helices[j][3]) + " -c " + trim(helices[j][4]) 
					+ " -a CA -s " + s_seed.str()+ ".0 -o " + cur_path_file;
				//cout << "\nCommand 2: " + command + '\n';
				GetStdoutFromCommand(command);
				n_printed++;

				#pragma omp critical
    				{	
					cout << "---------------------------------------------------" << endl;					
					cout << "Command: " << command << endl;					
					cout << "\nPrinted on File sucessfully!";
					cout << "\nEND...\n";
				}

				}
			}
			

			} //End for k
		}//End if min&class

	  }//End for j	
	}//End Parallel Region


	wtime	= omp_get_wtime() - wtime;	// Record the end time and calculate elapsed time
	cout << "Simulation took " << wtime/45 << " seconds per iteration with " << N_Threads << " threads" << endl;
        printStatistics(wtime);

	for (int i = 0; i < MAX_LINES; i++)
        	delete[] helices[i];

    	delete[] helices;


}


int main (int argc, char *argv[]) {
  
FILE * pFile;
long lSize;

omp_set_num_threads(32);

  if ( argc != 3 ){ // argc should be 2 for correct execution
    cout << "Program needs 2 parameters: [1]: Input pdb file, [2]: Output directory" << endl;
    exit(0);
  }else {
    pdbin = argv[1];
    output_dir = argv[2];
  }

  pFile = fopen ( pdbin.c_str() , "rb" );
  if (pFile==NULL) {fputs ("File error",stderr); exit (1);}

  // obtain file size:
  fseek (pFile , 0 , SEEK_END);
  lSize = ftell (pFile);
  rewind (pFile);

  // allocate memory to contain the whole file:
  buffer = (char*) malloc (sizeof(char)*lSize);
  if (buffer == NULL) {fputs ("Memory error",stderr); exit (2);}

  // copy the file into the buffer:
  size_buffer = fread (buffer,1,lSize,pFile);
  if (size_buffer != lSize) {fputs ("Reading error",stderr); exit (3);}

  fclose (pFile);

  //Start automatic Process
  executeAutomaticProcess();
  
  return 0;
}












