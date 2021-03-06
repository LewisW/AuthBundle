<?php

	namespace Vivait\AuthBundle\Controller;

	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Vivait\AuthBundle\Entity\Tenant;

	class TenantController extends Controller {
		public function indexAction() {
			################################################  SETTINGS  ################################################
			$repo = 'VivaitAuthBundle:Tenant';
			$twig = 'VivaitAuthBundle:Default:tenants.html.twig';
			############################################################################################################
			$db = $this->getDoctrine()
				->getRepository($repo)
				->findAll();

			$params[''] = '';
			return $this->render($twig, array('db' => $db, 'params' => $params));
		}

		public function editAction(Request $request) {
			################################################  SETTINGS  ################################################
			$name             = 'tenant';
			$repo             = 'VivaitAuthBundle:Tenant';
			$formtpl['title'] = 'Add/Edit ' . ucfirst($name);
			$obj              = new Tenant();
			$key              = $request->query->get('id', 0);
			$foreign_objs     = array( #	array(
				#		'repo'   => 'VivaBravoBundle:Product',
				#		'key'    => $request->query->get('pid', 0),
				#		'method' => 'setProduct',
				#		'name'   => 'product'),
			);
			############################################################################################################

			if(!$key) {
				### CREATING A NEW OBJECT ###

				#if there are foreign objects that should be bound to this object, bind them all here
				foreach($foreign_objs as $fo) {
					$foreign_obj = $this->getDoctrine()
						->getRepository($fo['repo'])
						->find($fo['key']);
					if(!$foreign_obj) {
						$this->get('session')->getFlashBag()->add('error', sprintf("Could not find the %s", $fo['name']));
						return $this->redirect($request->query->get('parent', $request->request->get('parent', $request->headers->get('referer'))));
					}
					call_user_func(array($obj, $fo['method'], $foreign_obj));
				}
			} else {
				### EDITING AN EXISTING OBJECT ###
				$obj = $this->getDoctrine()
					->getRepository($repo)
					->find($key);

				if(!$obj) {
					$this->get('session')->getFlashBag()->add('error', sprintf("Could not find the %s", $name));
				}
			}

			##############################################  CREATE FORM  ###############################################
			$form = $this->createFormBuilder($obj)
				->add('code', 'text', array('label' => 'Account'))
				->add('tenant', 'text', array('label' => 'Tenant'))
				->add('active', 'checkbox')
				->add('licenseduntil', 'datetime', array(
					'label'          => 'Licensed Until',
					'format'         => 'yyyy-MM-dd HH:mm',
					'widget'         => 'single_text',
					'datetimepicker' => true,
				))
				->add('users', 'entity', array(
					'class'    => 'VivaitAuthBundle:User',
					'property' => 'fullname',
					'multiple' => true,
					'attr'     => array('size' => 15),
					'required' => true,
					'label'    => 'Users',
				    'by_reference'=>false
				))
				->getForm();
			############################################################################################################

			if($request->isMethod('POST')) {
				$form->bind($request);
				if($form->isValid()) {
					$em = $this->getDoctrine()->getManager();
					$em->persist($obj);
					$em->flush();
					$this->get('session')->getFlashBag()->add('success', sprintf('The %s has been %s successfully', $name, $key ? 'modified' : 'created'));
					return $this->render('VivaitBootstrapBundle:Default:redirect.html.twig', array('redirect' => $request->query->get('parent', $request->request->get('parent', $request->headers->get('referer')))));
				}
			}
			if(isset($form)) {
				$formtpl['form'] = $form->createView();
			}
			$formtpl['action'] = $this->generateUrl($this->container->get('request')->get('_route'), $request->query->all());

			return $this->render('VivaitBootstrapBundle:Default:form.html.twig', array(
				'form' => array_merge($formtpl, array('parent' => $request->query->get('parent', $request->request->get('parent', $request->headers->get('referer')))))));
		}

		public function deleteAction(Request $request) {
			################################################  SETTINGS  ################################################
			$name         = 'tenant';
			$repo         = 'VivaitAuthBundle:Tenant';
			$id           = $request->query->get('id', 0);
			$msg_notfound = "The $name could not be found";
			$msg_success  = "The $name has been removed";
			############################################################################################################

			$obj = $this->getDoctrine()
				->getRepository($repo)
				->find($id);

			if(!$obj) {
				$this->get('session')->getFlashBag()->add('error', $msg_notfound);
			} else {
				$em = $this->getDoctrine()->getManager();
				$em->remove($obj);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', $msg_success);
			}

			return $this->redirect($request->headers->get('referer'));
		}
	}
