import urllib2, os, traceback
export_path = './travel_project/'
host = 'travel2.homevv.com'

#export_path = './cooperation_project/'
#host = 'lianyin.homevv.com'

path = urllib2.urlopen( 'http://'+host+'/myplan.php' )
#path = open( 'error_file.log', 'r' )
path_list = path.readlines()


ignore_list = [
				'./55566677', 
				'./9999999999', 
				'./88888888888888888888888889999', 
				'./34', 
				'./2011', 
				'./2011_bak', 
				'./2012', 
				'./2012_bak', 
				'./test_111', 
				'./templates_c',
                                './data'
##				'./include/config.inc.php', 
				'.svn'
				]

log_file = open('error.log', 'w+')


def superMkdir( path ):
	if not os.path.isdir( path ):
		_path = os.path.split( path )
		if _path[0] and not os.path.isdir( _path[0] ):
			superMkdir( _path[0] )
		os.mkdir( path )
	return path
	
for file in path_list:
	is_ignore = 0
	for ignore_file in ignore_list:
		if ignore_file in file:
			is_ignore = 1
			break
	if is_ignore == 1:
		continue
	print file
	file = str.strip(file)
	try_times = 0
	while try_times < 5:
		try_times += 1
		try:
			code_file = urllib2.urlopen( 'http://'+host+'/myplan.php?action=download&getf='+file )
			source_code = code_file.read()
			code_file.close()
			if file+' is folder!' in source_code:
				superMkdir( export_path+file )
			else:
				_path = os.path.split( export_path+file )
				if _path[0] and not os.path.isdir( _path[0] ):
					superMkdir( _path[0] )
				dest_file = open( export_path+file, 'wb+' )
				dest_file.write( source_code )
				dest_file.close()
			break
		except Exception, e:
			if try_times >= 5:
				log_file.write(file+'\n')
				log_file.write(traceback.format_exc())
		
		
log_file.close()
