timestamps {
    

node () {

	stage ('Memorama - Checkout') {
 	    checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [], submoduleCfg: [], userRemoteConfigs: [[credentialsId: 'chiki1', url: 'https://github.com/chikimoco/Memorama.git']]]) 
	}
	stage ('Memorama - Build') {
 		environment {
            PATH = PATH + ";C:\\Windows\\System32\\"
        }
        bat label: '', script: '''phpunit core/test/'''
	}
	stage ('Memorama - Deploy'){
        fileOperations([fileCopyOperation(excludes: '', flattenFiles: false, includes: '**', targetLocation: 'C:\\wamp64\\www\\MemoDeploy')])
	}
}
}
