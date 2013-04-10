import re
f = open('error.log','r')
error_log = f.readlines()
f.close()

for error in error_log:
    error_file = re.findall('^(.*)Traceback \(most recent call last\):$',error)
    if error_file:
        print error_file[0]
