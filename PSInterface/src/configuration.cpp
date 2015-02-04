#include <map>
#include <string>
#include <fstream>
#include <iostream>
#include <stdlib.h>

using namespace std;

class Configuration
{
public:
    // clear all values
    void Clear();

    // load a configuration file
    bool Load(const string& File);

    // check if value associated with given key exists
    bool Contains(const string& key) const;

    // get value associated with given key
    bool Get(const string& key, string& value) const;
    bool Get(const string& key, int&    value) const;
    bool Get(const string& key, long&   value) const;
    bool Get(const string& key, double& value) const;
    bool Get(const string& key, bool&   value) const;

private:
    // the container
    map<string,string> data;

    // remove leading and trailing tabs and spaces
    static string Trim(const string& str);
};


// ----------------------------------
// method implementations
// ----------------------------------

void Configuration::Clear()
{
    data.clear();
}

bool Configuration::Load(const string& file)
{
    ifstream inFile(file.c_str());

    if (!inFile.good())
    {
        cout << "Cannot read configuration file " << file << endl;
        return false;
    }

    while (inFile.good() && ! inFile.eof())
    {
        string line;
        getline(inFile, line);

        // filter out comments
        if (!line.empty())
        {
            int pos = line.find('#');

            if (pos != string::npos)
            {
                line = line.substr(0, pos);
            }
        }

        // split line into key and value
        if (!line.empty())
        {
            int pos = line.find('=');

            if (pos != string::npos)
            {
                string key     = Trim(line.substr(0, pos));
                string value   = Trim(line.substr(pos + 1));

                if (!key.empty() && !value.empty())
                {
                    data[key] = value;
                }
            }
        }
    }

    return true;
}

bool Configuration::Contains(const string& key) const
{
    return data.find(key) != data.end();
}

bool Configuration::Get(const string& key, string& value) const
{
    map<string,string>::const_iterator iter = data.find(key);

    if (iter != data.end())
    {
        value = iter->second;
        return true;
    }
    else
    {
        return false;
    }
}

bool Configuration::Get(const string& key, int& value) const
{
    string str;

    if (Get(key, str))
    {
        value = atoi(str.c_str());
        return true;
    }
    else
    {
        return false;
    }
}

bool Configuration::Get(const string& key, long& value) const
{
    string str;

    if (Get(key, str))
    {
        value = atol(str.c_str());
        return true;
    }
    else
    {
        return false;
    }
}

bool Configuration::Get(const string& key, double& value) const
{
    string str;

    if (Get(key, str))
    {
        value = atof(str.c_str());
        return true;
    }
    else
    {
        return false;
    }
}

bool Configuration::Get(const string& key, bool& value) const
{
    string str;

    if (Get(key, str))
    {
        value = (str == "true");
        return true;
    }
    else
    {
        return false;
    }
}

string Configuration::Trim(const string& str)
{
    int first = str.find_first_not_of(" \t");

    if (first != string::npos)
    {
        int last = str.find_last_not_of(" \t");

        return str.substr(first, last - first + 1);
    }
    else
    {
        return "";
    }
}
