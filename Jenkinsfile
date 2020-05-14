timestamps {
    

node () {

	stage ('Memorama - Checkout') {
 	    checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [], submoduleCfg: [], userRemoteConfigs: [[credentialsId: '', url: 'https://github.com/uliancona98/MemoramaEntrega']]]) 
	}
	stage ('Memorama - Build') {
        bat 'call vendor/bin/phpunit.bat phpunit core/test/'
	}
	stage ('Memorama - Deploy'){
        fileOperations([fileCopyOperation(excludes: '', flattenFiles: false, includes: '**', targetLocation: 'C:\\xampp\\htdocs\\MemoramaEntrega')])
	}
}
}
