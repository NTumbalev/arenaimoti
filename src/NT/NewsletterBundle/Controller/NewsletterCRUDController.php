<?php

namespace NT\NewsletterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use NT\CoreBundle\Controller\TreeCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sonata\AdminBundle\Exception\ModelManagerException;

class NewsletterCRUDController extends TreeCRUDController
{
    public function mailChimpStatusAction() {
		$em = $this->getDoctrine()->getManager();

		$mailChimpObject = $em->getRepository('NTMailChimpBundle:MailChimp')->findOneById(1);
		if ($mailChimpObject != null && $mailChimpObject->getListId() != null && $mailChimpObject->getListId() != '') {
			$mc = new \NT\MailChimpBundle\Services\MailChimp($mailChimpObject->getApiKey());
			$listMembers = $mc->getList($mailChimpObject->getListId())->getAllListMembers();

			$newsletterRepo = $em->getRepository('NTNewsletterBundle:Newsletter');
			foreach ($listMembers->members as $mcMember) {
				$newsletter = $newsletterRepo->findOneByEmail($mcMember->email_address);
				if ($newsletter != null) {
					$newsletter->setMailChimpStatus($mcMember->status);
					$em->persist($newsletter);
					$em->flush();
				}
			}
		}

		return $this->redirect('list');
    }
}
