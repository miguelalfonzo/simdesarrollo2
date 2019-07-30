<?php

use \Observer\Transaction;
use \Observer\SoftDelete;

use \Dmkt\Solicitud;
use \Dmkt\SolicitudDetalle;
use \Dmkt\SolicitudClient;
use \Dmkt\SolicitudProduct;
use \Dmkt\SolicitudGer;
use \Common\Deposit;
use \Expense\Entry;
use \Fondo\Fondo;
use \Dmkt\Periodo;
use \System\SolicitudHistory;
use \Expense\Expense;
use \Expense\ExpenseItem;
use \Expense\ProofType;
use \Dmkt\Account;
use \Expense\Mark;
use \Expense\MarkProofAccounts;
use \Common\FileStorage;
use \Users\TemporalUser;
use \Dmkt\InvestmentType;
use \Dmkt\Activity;
use \Dmkt\InvestmentActivity;
use \Fondo\FondoSupervisor;
use \Fondo\FondoGerProd;
use \Fondo\FondoInstitucional;
use \Fondo\FondoMktPeriodHistory;
use \System\FondoMktHistory;
use \Devolution\Devolution;
use \Devolution\DevolutionHistory;
use \Event\Event;
use \Fondo\FondoSubCategoria;
use \Dmkt\SpecialAccount;

use \PPTO\PPTOSupervisor;

use \Parameter\SolicitudExclution;
	
	//SAVE USER_ID FOR CREATED_BY & UPDATED_BY
	Solicitud::observe(				new Transaction());
	SolicitudDetalle::observe(		new Transaction());
	SolicitudHistory::observe(		new Transaction());
	SolicitudClient::observe(		new Transaction()); 
	SolicitudProduct::observe(		new Transaction()); 
	SolicitudGer::observe(			new Transaction());
	Deposit::observe(				new Transaction());
	Entry::observe(					new Transaction());
	Fondo::observe(					new Transaction());
	Periodo::observe(				new Transaction());
	Expense::observe(				new Transaction());
	ExpenseItem::observe(			new Transaction());
	ProofType::observe(				new Transaction());
	Account::observe( 				new Transaction());
	Mark::observe( 					new Transaction());
	MarkProofAccounts::observe( 	new Transaction());
	FondoSupervisor::observe(   	new Transaction());
	FondoGerProd::observe( 			new Transaction());
	FondoInstitucional::observe(	new Transaction());
	FondoSubCategoria::observe( 	new Transaction());
	FondoMktHistory::observe( 		new Transaction());
	FondoMktPeriodHistory::observe( new Transaction());
	Devolution::observe( 			new Transaction());
	DevolutionHistory::observe(     new Transaction());
	Event::observe( 				new Transaction());
	
	//SOFT DELETE
	Activity::observe(          	new SoftDelete());
	TemporalUser::observe(      	new SoftDelete());
	InvestmentType::observe(    	new SoftDelete());
	InvestmentActivity::observe(	new SoftDelete());
	SolicitudExclution::observe(    new SoftDelete());
	SpecialAccount::observe(        new SoftDelete());
	
