-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: memorama
-- ------------------------------------------------------
-- Server version	5.6.26-log
--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
CREATE TABLE `materias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
INSERT INTO `materias` VALUES (1,'filosofia cuantica'),(2,'Semat');
UNLOCK TABLES;

--
-- Table structure for table `parejas`
--

DROP TABLE IF EXISTS `parejas`;
CREATE TABLE `parejas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmateria` int(11) NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idmateria_idx` (`idmateria`),
  CONSTRAINT `idmateria` FOREIGN KEY (`idmateria`) REFERENCES `materias` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `parejas`
--

LOCK TABLES `parejas` WRITE;
INSERT INTO `parejas` VALUES (1,1,'concepto1','descripcion1'),(2,1,'concepto2','descripcion2'),(3,1,'pesiria','cantollanes'),(4,1,'concepto1','descripcion1'),(5,1,'concepto2','descripcion2'),(6,1,'pesiria','cantollanes'),(7,1,'concepto1','descripcion1'),(8,1,'concepto2','descripcion2'),(9,1,'pesiria','cantollanes'),(10,2,'Semat','Software Engineering Method and Theory.'),(11,2,'Alphas','Representations of the essential things to work with.'),(12,2,'Activity Spaces','Representations of the essential things to do.'),(13,2,'Customer','Area of concern the team needs to understand the stakeholders and the opportunity to be addressed'),(14,2,'Solution','Area of concern the team needs to establish a share understanding of the requirements, and implement, build, test, deploy and support a software system.'),(15,2,'Endeavor','Area of concern the team and its way-of-working have to be formed, and the work has to be done.'),(16,2,'Opportunity','The set of circumstances that makes it appropriate to develop or change a software system.'),(17,2,'Stakeholders','The people, groups, or organizations who affect or are affected by a software system.'),(18,2,'Requirements','What the software system must do to address the opportunity and satisfy the stakeholders.'),(19,2,'Software System','A system made up of software, hardware, and data that provides its primary value by the execution of the software.'),(20,2,'Work','Activity involving mental or physical effort done in order to achieve a result.'),(21,2,'Team','The group of people actively engaged in the development, maintenance, delivery and support of a specific software system.'),(22,2,'Way of work','The tailored set of practices and tools used by a team to guide and support their work.'),(23,2,'Stakeholder Representation','This competency encapsulates the ability to gather, communicate, and balance the needs of other stakeholders, and accurately represent their views.'),(24,2,'Analysis','This competency encapsulates the ability to understand opportunities and their related stakeholder needs, and transform them into an agreed upon and consistent set of  requirements.'),(25,2,'Development','This competency encapsulates the ability to design and program effective software systems following the standards and norms agreed upon by the team.'),(26,2,'Testing','This competency encapsulates the ability to test a system, verifying that it is usable and that it meets the requirements.'),(27,2,'Leadership','This competency enable a person to inspire and motivate a group of people to achieve a successful conclusion to their work and to meet their objectives.'),(28,2,'Management','This competency encapsulates the ability to coordinate, plan, and track the work done by a team.');
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT 'usuario',
  `tipo` int(1) NOT NULL DEFAULT '0',
  `clave` varchar(100) NOT NULL DEFAULT 'memopass',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
INSERT INTO `usuario` VALUES (1,'pepe',0,'camello');
UNLOCK TABLES;

